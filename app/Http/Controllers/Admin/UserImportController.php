<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class UserImportController extends Controller
{
    /**
     * ==============================================================
     * MAPAS FIXOS
     * ==============================================================
     */

    /** Perfis PT-BR → sistema */
    private const ROLE_MAP = [
        'Usuário' => 'user',
        'Operador' => 'agent',
        'Administrador' => 'admin',
    ];

    /** Cabeçalhos esperados */
    private const HEADER_MAP = [
        'Nome' => 'name',
        'E-mail' => 'email',
        'Perfil' => 'role',
        'Departamento' => 'department',
        'Cargo' => 'job_title',
        'Telefone' => 'phone',
        'Ativo' => 'is_active',
        'Senha' => 'password',
    ];

    /**
     * ==============================================================
     * DOWNLOAD DA PLANILHA MODELO
     * ==============================================================
     */
    public function downloadTemplate(): StreamedResponse
    {
        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Usuarios');

        foreach (array_keys(self::HEADER_MAP) as $i => $header) {
            $sheet->setCellValueByColumnAndRow($i + 1, 1, $header);
        }

        $sheet->getStyle('A1:H1')->getFont()->setBold(true);

        /** Validação Perfil */
        $profileValidation = new DataValidation;
        $profileValidation->setType(DataValidation::TYPE_LIST);
        $profileValidation->setFormula1('"Usuário,Operador,Administrador"');
        $sheet->setDataValidation('C2:C1000', $profileValidation);

        /** Validação Ativo */
        $activeValidation = new DataValidation;
        $activeValidation->setType(DataValidation::TYPE_LIST);
        $activeValidation->setFormula1('"1,0"');
        $sheet->setDataValidation('G2:G1000', $activeValidation);

        return new StreamedResponse(function () use ($spreadsheet) {
            (new Xlsx($spreadsheet))->save('php://output');
        }, 200, [
            'Content-Disposition' => 'attachment; filename="modelo_importacao_usuarios.xlsx"',
        ]);
    }

    /**
     * ==============================================================
     * PREVIEW DA IMPORTAÇÃO (NÃO SALVA)
     * ==============================================================
     */
    public function preview(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls',
        ]);

        $spreadsheet = IOFactory::load($request->file('file'));
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray(null, true, true, true);

        /** Cabeçalho */
        $headerRow = array_shift($rows);
        $headers = [];

        foreach ($headerRow as $col => $value) {
            if (isset(self::HEADER_MAP[trim($value)])) {
                $headers[$col] = self::HEADER_MAP[trim($value)];
            }
        }

        $preview = [];
        $emails = [];
        $names = [];

        foreach ($rows as $index => $row) {
            $line = $index + 2;
            $data = [];
            $errors = [];

            foreach ($headers as $col => $field) {
                $data[$field] = trim((string) ($row[$col] ?? ''));
            }

            if (empty($data['name'])) {
                $errors[] = 'Nome não informado';
            }

            if (empty($data['email'])) {
                $errors[] = 'E-mail não informado';
            }

            if (! empty($data['email']) && isset($emails[$data['email']])) {
                $errors[] = 'E-mail duplicado na planilha';
            }

            if (! empty($data['name']) && isset($names[$data['name']])) {
                $errors[] = 'Nome duplicado na planilha';
            }

            if (! isset(self::ROLE_MAP[$data['role'] ?? ''])) {
                $errors[] = 'Perfil inválido';
            }

            if (! empty($data['department']) &&
                ! Department::where('name', $data['department'])->exists()) {
                $errors[] = 'Departamento não encontrado';
            }

            if (! empty($data['email']) &&
                User::where('email', $data['email'])->exists()) {
                $errors[] = 'E-mail já existe no sistema';
            }

            $emails[$data['email']] = true;
            $names[$data['name']] = true;

            $preview[] = [
                'line' => $line,
                'data' => $data,
                'errors' => $errors,
                'valid' => empty($errors),
            ];
        }

        session(['import_preview' => $preview]);

        return view('admin.users.import-preview', compact('preview'));
    }

    /**
     * ==============================================================
     * CONFIRMAÇÃO DA IMPORTAÇÃO
     * ==============================================================
     */
    public function confirm()
    {
        $preview = session('import_preview');

        if (! $preview) {
            return redirect()->route('admin.users.index')
                ->withErrors('Nenhuma importação em andamento.');
        }

        DB::beginTransaction();

        try {
            $imported = 0;

            foreach ($preview as $row) {
                if (! $row['valid']) {
                    continue;
                }

                $data = $row['data'];

                User::create([
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'role' => self::ROLE_MAP[$data['role']],
                    'department_id' => Department::where('name', $data['department'])->value('id'),
                    'job_title' => $data['job_title'] ?: null,
                    'phone' => $data['phone'] ?: null,
                    'is_active' => (bool) $data['is_active'],
                    'password' => Hash::make($data['password'] ?: '123456'),
                ]);

                $imported++;
            }

            DB::commit();
            session()->forget('import_preview');

            return redirect()
                ->route('admin.users.index')
                ->with('success', "Importação concluída: {$imported} usuários importados.");

        } catch (\Throwable $e) {
            DB::rollBack();
            report($e);

            return back()->withErrors('Erro ao confirmar importação.');
        }
    }
}
