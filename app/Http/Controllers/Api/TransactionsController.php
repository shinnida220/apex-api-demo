<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Account;
use App\Models\Transaction;
use Illuminate\Support\Facades\Validator;

class TransactionsController extends Controller
{
    public function index(Request $request){
        return $this->successResponse('Api ping successful');
    }

    /** 
     * We need the bankCode and the accountNumber
     */
    public function validateTransaction(Request $request) {
        $params = $request->all();
        $validator = Validator::make($params, [
            'bankCode' => 'bail|required|min:3|max:3',
            'accountNumber' => 'bail|required|min:10|max:10',
        ]);

        if ($validator->fails()){
            return $this->errorResponse('Operation failed. Please see the error node for details', $validator->errors());
        } else if (!$this->validateNuban($params['accountNumber'], $params['bankCode'] )) {
            return $this->errorResponse('Operation failed. Account number is not a valid Nuban account number');
        } 

        // Lets retrieve the account number
        $account = Account::where('accountNumber', $params['accountNumber'])->first();
        if (!$account) return $this->errorResponse('Operation failed. Account number not found.');

        // Lets create a tx.
        $Transaction = new Transaction();
        $tx = $Transaction::create([
            'account_id' => $account->id,
            'transactionRef' => $Transaction->generateTransactionRef(24),
        ]);

        // Return the corresponding account name stored against the nuban account number
        return $this->successResponse('Transaction initiated', ['accountName' => $account->accountName, 'accountNumber' => $account->accountNumber, 'transactionRef' => $tx->transactionRef]);
    }

    public function completeTransaction(Request $request) {
        $params = $request->all();

        $validator = Validator::make($params, [
            'transactionRef' => 'bail|required|exists:transactions',
            'amount' => 'bail|required|numeric'
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Operation failed. Please see the error node for details', $validator->errors());
        }

        // Lets be sure the transaction hasn't be completed..
        $tx = Transaction::with('account')->where('transactionRef', $params['transactionRef'])->first();
        if (!empty($tx->transactionStatus)) {
            return $this->errorResponse('Operation failded. Duplicate transaction not allowed.');
        } else if (abs($params['amount']) > $tx->account->accountBalance ) {
            return $this->errorResponse('Operation failded. Insufficient account balance.');
        }

        // Update the user's account balance.
        $tx->account->accountBalance -= abs($params['amount']);
        $tx->account->save();

        // Update the tx..
        $tx->amount = $params['amount'];
        $tx->transactionStatus = 1;
        $tx->save();


        //reload without the account details
        $tx = Transaction::find($tx->id);

        return $this->successResponse('Transaction successful. Visit /api/transactions to view list of transactions', ['transaction' => $tx]);
    }

    public function verifyTransaction(Request $request, $txRef = null) {
        if (empty($txRef)) return $this->errorResponse('Invalid transaction reference.');
        $tx = Transaction::where('transactionRef', $txRef)->first();

        if (!$tx) return $this->errorResponse('Transaction not found.');
        return $this->successResponse('Transaction retrieved successfully', [
            'transactionStatus' => (empty($tx->transactionStatus) ? 'pending' : 'completed'),
            'transactionRef' => $txRef,
            'amount' => number_format( $tx->amount, 2)
        ] );
    }

    /** 
     * Miscellaneous..
     */
    public function listAccounts(Request $request) {
        $accounts = Account::all();
        return $this->successResponse('List of available accounts for testing. Visit /api/create-account to setup a test account', ['total' => count($accounts), 'accounts' => $accounts]);
    }

    public function listTransactions(Request $request) {
        $transactions = Transaction::with('account')->get();
        return $this->successResponse('List of available transactions retrieved. Visit /api/create-account to setup a test account', ['total' => count($transactions), 'transactions' => $transactions]);
    }

    public function createAccount(Request $request) {
        $params = $request->all();
        $validator = Validator::make($params, [
            'bankCode' => 'bail|required',
            'accountNumber' => 'bail|required|min:10|max:10',
            'accountName' => 'bail|required|min:5'
        ], [
            'bankCode.required' => 'Please use either, Access Bank 044, Fidelity Bank 070, StanbicIBTC 221, Afribank 014, Finbank 085, Standard Chartered Bank 068, Citibank 023, Guaranty Trust Bank 058, Sterling Bank 232, Diamond Bank 063, Intercontinental Bank 069, United Bank for Africa 033, Ecobank 050, Oceanic Bank 056, Union Bank 032, Equitorial Trust Bank 040 14. BankPhb 082 22. Wema bank 035, First Bank 011, Skye Bank 076, Zenith Bank 057, FCMB 214, SpringBank 084 or Unity bank 215',
            'accountName' => 'A valid 10 digit account number is required.',
            'accountName' => 'A valid account name is required.'
        ]);

        if ($validator->fails()){
            return $this->errorResponse('Operation failed. Please see the error node for details', $validator->errors());
        } else if (!$this->validateNuban($params['accountNumber'], $params['bankCode'] )) {
            return $this->errorResponse('Operation failed. Account number is not a valid Nuban account number');
        }

        $duplicate = Account::where('accountNumber', $params['accountNumber'])->count();
        if ($duplicate > 0) {
            return $this->errorResponse('Operation failed. Account number already esists. Please visit /api/accounts to see a list of available accounts.');
        }

        $account = Account::create([
            'accountNumber' => $params['accountNumber'],
            'accountName' => $params['accountName'],
            'accountBalance' => mt_rand(1000,100000)
        ]);

        return $this->successResponse('Account was added successfully. To see a list of all accounts, visit /api/accounts', compact('account'));

    }
}
