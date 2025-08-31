<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class PdfController extends Controller
{
    public function export(Request $request)
    {
        $user = $request->user();

        // Build a simple HTML (no Blade) based on your users table
        $html = <<<HTML
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>FinnTerest — Profile</title>
<style>
  @page { margin: 24px; }
  body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
  h1 { font-size: 18px; margin: 0 0 8px; }
  .muted { color:#555; font-size: 11px; margin-bottom: 12px; }
  .card { border:1px solid #ddd; border-radius:8px; padding:12px; }
  table { width:100%; border-collapse: collapse; }
  td { padding:6px 0; vertical-align: top; }
  .label { width:180px; font-weight:bold; }
</style>
</head>
<body>
  <h1>FinnTerest — User Report</h1>
  <div class="muted">Generated on {$this->esc(now()->format('d M Y, h:i A'))}</div>

  <div class="card">
    <table>
      <tr><td class="label">Name</td><td>{$this->esc($user->name)}</td></tr>
      <tr><td class="label">Email</td><td>{$this->esc($user->email)}</td></tr>
      <tr><td class="label">Role</td><td>{$this->esc($user->role)}</td></tr>
      <tr><td class="label">Job</td><td>{$this->esc($user->job ?? '—')}</td></tr>
      <tr><td class="label">Salary</td><td>{$this->money($user->salary)}</td></tr>
      <tr><td class="label">Expenditure</td><td>{$this->money($user->expenditure)}</td></tr>
      <tr><td class="label">Budget</td><td>{$this->money($user->budget)}</td></tr>
      <tr><td class="label">Joined</td><td>{$this->esc($user->created_at?->format('d M Y'))}</td></tr>
    </table>
  </div>
</body>
</html>
HTML;

        $pdf = Pdf::loadHTML($html)->setPaper('a4');

        // Send as an attachment so your existing code downloads it
        $filename = 'finnterest-summary-'.$user->id.'.pdf';
        return $pdf->download($filename);
    }

    private function esc($v): string
    {
        return htmlspecialchars((string)$v ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
    private function money($n): string
    {
        return $n !== null ? number_format((float)$n, 2, '.', ',') : '—';
    }
}
