Using any stack of your choice, develop an API for intra-bank transaction processing with the following requirements-

1. An endpoint for transaction validation with the following checks: \**i. Check that the account number is NUBAN and exists on your database
   *ii. Return the corresponding account name stored against the nuban account number

2. An endpoint for completing the transaction with the following features:
   *i. It should accept the same reference used for validation
   *ii. Duplicate transactions should not be processed
   *iii. Available balance should be enough to accommodate the transaction amount
   *iv. Return the right response where balance is not enough
   *v. Return a generic failed response for other non-successful conditions
   *vi. Return successful response when transaction meets successful requirements

3. \*An endpoint for status check or requery
