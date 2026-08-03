# Refactor Plan

## QuoteEngine
- generate()
- rank()
- persist()

Split into:
- QuoteCalculator
- OfferRanker
- QuoteRepository

## PolicyWorkflowService
Analysis only

## PaymentService
Analysis only

## FormulaExecutor
Analysis only
