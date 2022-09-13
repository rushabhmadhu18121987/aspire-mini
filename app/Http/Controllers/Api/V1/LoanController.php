<?php

namespace App\Http\Controllers\Api\V1;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Validator;
use App\Models\Loan;
use App\Models\LoanEmi;

class LoanController extends Controller {
    
    /* Apply for Loan */
    public function applyLoan(Request $request) {
        $validator = Validator::make($request->all(), [
            'amount' => 'required|integer|min:100|max:500000',
            'term' => 'required|integer|min:3',
        ]);

        if ($validator->fails()) {
            return $this->setFailureResponse(false, 'Invalid Inputs', $validator->errors());
        }
        
        try {
            DB::beginTransaction();
            $loan = new Loan();
            $loan->user_id  = $request->user()->id;
            $loan->amount   = intval($request->amount);
            $loan->duration = intval($request->term);
            $loan->status   = Loan::$LOAN_STATUS_PENDING_ID;
            $loan->set_createdAt();
            $loan->save();

            if(LoanEmi::generateLoanEmi($loan->id, $request->amount, $request->term)) {
                DB::commit();
                return response()->json([
                    "status" => true,
                    "message" => "Loan Applied successfully",
                    "data" => $loan
                ], 200);
            }
            DB::rollBack();
            $this->setFailureResponse(false, 'Loan EMI Creation failed.', '');
        } catch (\Exception $ex) {
            DB::rollBack();
            return $this->setFailureResponse(false, 'Exception Handled', $ex->getMessage());
        }     
    }

    /**
     * Display a listing of the loans.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        try {
            $loans = Loan::with('user')->where('status', 0)->get();
            $loanList = [];
            foreach($loans as $loan) {
                // print_r($loan);die;
                $loanList[] = $loan->getFormattedLoan();
            }
            $responseData = [
                "status" => true,
                "message" => "All Approval Pending Loans",
                "data" => $loanList
            ];
            return response()->json($responseData, 200);
        } catch (\Exception $ex) {
            return $this->setFailureResponse(false, 'Exception Handled', $ex->getMessage());
        }
    }

    //Approve Loan By ID
    public function approveLoan(Request $request, $id) {
        try {
            $loan = Loan::find($id);
            $loan->status = Loan::$LOAN_STATUS_APPROVED_ID;
            $loan->save();
            $responseData = [
                "status" => true,
                "message" => "Loan Approved successfully",
                "data" => $loan
            ];
            return response()->json($responseData, 200);
        } catch (\Exception $ex) {
            return $this->setFailureResponse(false, 'Exception Handled', $ex->getMessage());
        }
    }

    //Get All Loan Emis 
    public function loanEmis(Request $request) {
        try {
            $id   = intval($request->get('id'));
            $loan = Loan::find($id);
            if(!$loan) {
                return $this->setFailureResponse(false, 'Loan ID not found', 'Invalid Param');
            }
            $responseData = [
                "status" => true,
                "message" => "Loan Emis are listed here",
                "data" => $loan->formattedEmis()
            ];
            return response()->json($responseData, 200);
        } catch (\Exception $ex) {
            return $this->setFailureResponse(false, 'Exception Handled', $ex->getMessage());
        }
        
    }
    
    /**
     * Store a EMI to database for the Same amount of EMI (FYI : Not managed for EMI Paid Amount greater or less)
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function store(Request $request) {
        $request_data = $request->all();
        
        $validator = Validator::make($request_data, [
            'loan_id' => 'required',
            'amount' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid Inputs',
                'error' => $validator->errors()
            ]);
        }
        $loan = Loan::find($request->loan_id);
        if ($loan->getLoanStatus() == Loan::$LOAN_STATUS_APPROVED) {
            $emi = $loan->getFirstUpaidEmi();
            if(!is_null($emi)) {
                if ($request->amount == $emi->emi_amount) {
                    $emi->status = LoanEmi::$LOAN_EMI_STATUS_PAID_ID;
                    $emi->set_updatedAt();
                    $emi->save();
                    $pendingEmiCount = LoanEmi::where('status',LoanEmi::$LOAN_EMI_STATUS_PENDING_ID)->count();
                    if($pendingEmiCount==0) {
                        $loan->status = Loan::$LOAN_STATUS_PAID_ID;
                        $loan->save();
                    }
                    return response()->json([
                        'status' =>true,
                        'message' => 'EMI Paid Successfully',
                        'data' => $emi
                    ]);
                } else {
                    return $this->setFailureResponse(false, "EMI Amount is not the same","Please enter the same amount as EMI amount.");
                }
            } else {
                return $this->setFailureResponse(false, "Facing Issue in Loan EMI","Unable to find EMI from the database.");
            }
        } else {
            return $this->setFailureResponse(false, "Loan EMI can not be paid.","Loan current status is : ".$loan->getLoanStatus());
        }
    }

    //Private Member function to return failure response
    private function setFailureResponse($status, $message, $error)  {
        return response()->json([
            'status' => $status,
            'message' => $message,
            'error' => $error
        ], 401);
    }
}