$files = @(
    ".\app\Domain\Formula\ExpressionResolver.php",
    ".\app\DTOs\QuoteRequestDTO.php",
    ".\app\Services\Formula\FormulaExecutor.php",
    ".\app\Services\Payment\PaymentService.php",
    ".\app\Services\Quote\PremiumCalculator.php",
    ".\app\Services\Workflow\WorkflowEngine.php"
)

$totalViolations = 0

foreach ($f in $files) {
    Write-Host ""
    Write-Host "===== $f ====="

    $lines = Get-Content $f
    $currentMethod = ""
    $depth = 0
    $body = ""
    $insideMethod = $false

    foreach ($line in $lines) {

        if (-not $insideMethod) {
            if ($line -match '\bfunction\s+([A-Za-z_][A-Za-z0-9_]*)\s*\(') {
                $currentMethod = $Matches[1]
                $insideMethod = $true
                $body = $line
                $depth = ([regex]::Matches($line, '\{')).Count -
                         ([regex]::Matches($line, '\}')).Count
            }
        }
        else {
            $body += "`n" + $line

            $depth += ([regex]::Matches($line, '\{')).Count
            $depth -= ([regex]::Matches($line, '\}')).Count

            if ($depth -le 0) {

                $branchCount = [regex]::Matches(
                    $body,
                    '\b(if|elseif|for|foreach|while|case|catch)\b'
                ).Count

                $logicCount = [regex]::Matches(
                    $body,
                    '&&|\|\|'
                ).Count

                $complexity = 1 + $branchCount + $logicCount

                if ($complexity -ge 10) {
                    Write-Host "VIOLATION: $currentMethod :: COMPLEXITY=$complexity :: BRANCHES=$branchCount :: LOGIC=$logicCount"
                    $totalViolations++
                }

                $insideMethod = $false
                $currentMethod = ""
                $body = ""
                $depth = 0
            }
        }
    }
}

Write-Host ""
Write-Host "TOTAL_METHOD_COMPLEXITY_VIOLATIONS=$totalViolations"
