<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Loan extends Model {
    use \App\Traits\ModelTrait;
    public $timestamps = false;

     /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'amount',
        'duration',
        'total_int_amount',
        'status',
        'approved_by',
        'approved_at',
        'created_at',
        'updated_at'
    ];

    //Some of Model Constants to return Static Status Values
    public static $LOAN_STATUS_PENDING = 'PENDING APPROVAL';
    public static $LOAN_STATUS_APPROVED = 'APPROVED';
    public static $LOAN_STATUS_PAID = 'LOAND PAID';

    public static $LOAN_STATUS_PENDING_ID = 0;
    public static $LOAN_STATUS_APPROVED_ID= 1;

    //Model relation ship (Belongs To One) Loan Belongs To Single User (Note: Only Single user as of now. FYI : no joint relationship)
    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function approvedByUser() {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function emis() {
        return $this->hasMany(LoanEmi::class, 'loan_id');
    }

    //Other General Functions
    public function getLoanStatus() {
        switch($this->status) {
            case 1:
                return self::$LOAN_STATUS_APPROVED;
                break;
            case 2:
                return self::$LOAN_STATUS_PAID;
                break;
            case 0:
                return self::$LOAN_STATUS_PENDING;
                break;
        }
    }

    //Format Emi list
    public function formattedEmis() {
        $emiList = [];
        foreach($this->emis as $key => $emi) {
            $emiList[$key]['emi_amount']    = $emi->emi_amount;
            $emiList[$key]['emi_intrest']   = $emi->emi_intrest;
            $emiList[$key]['emi_principal'] = $emi->emi_principal;
            $emiList[$key]['emi_due_date']  = date('Y-m-d', $emi->emi_due_date);
            $emiList[$key]['status']        = $emi->getLoanEmiStatus();
            $emiList[$key]['emi_type']      = $emi->getEmiType();
        }
        return $emiList;
    }

    //Formated Loan Data
    public function getFormattedLoan() {
        $loanObj = [];
        $loanObj['loan_applied_by']     = $this->user->name;
        $loanObj['loan_amount']         = $this->amount;
        $loanObj['loan_term']           = $this->duration;
        $loanObj['loan_applied_date']   = $this->get_createdAt();
        $loanObj['loan_status']         = $this->getLoanStatus();
        $loanObj['loan_id']             = $this->id;
        return $loanObj;
    }

    //Get First Upaid EMI from the Emis
    public function getFirstUpaidEmi() {
        return LoanEmi::where(['loan_id'=>$this->getId(),'status'=>LoanEmi::$LOAN_EMI_STATUS_PENDING_ID])->orderBy('id')->first();        
    }
}