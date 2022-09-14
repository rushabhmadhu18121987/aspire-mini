<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoanEmi extends Model {
    use \App\Traits\ModelTrait;
    public $timestamps = false;
     /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'loan_id',
        'emi_amount',
        'emi_intrest',
        'emi_principal',
        'emi_due_date',
        'status',
        'is_regular_emi',
        'created_at',
        'updated_at'
    ];

    public static $LOAN_EMI_STATUS_PENDING = 'PENDING';
    public static $LOAN_EMI_STATUS_PAID = 'PAID';

    public static $LOAN_EMI_STATUS_PENDING_ID = 0;
    public static $LOAN_EMI_STATUS_PAID_ID = 1;

    public static $LOAN_EMI_TYPE_REGULAR = 'EMI';
    public static $LOAN_EMI_TYPE_ADDITIONAL = 'ADDITIONAL REPAYMENT';

    //Model relation ship (Belongs To One) 
    public function loan() {
        return $this->belongsTo(Loan::class, 'id');
    }

    //Other General Functions
    public function getLoanEmiStatus() {
        switch($this->status) {
            case 1:
                return self::$LOAN_EMI_STATUS_APPROVED;
                break;
            case 0:
                return self::$LOAN_EMI_STATUS_PENDING;
                break;
            default:
                return 'NA';
        }
    }

    //Fetch EMI Type (Regular or Additional Payment)
    public function getEmiType() {
        return ($this->is_regular_emi == 1) ? self::$LOAN_EMI_TYPE_REGULAR : self::$LOAN_EMI_TYPE_ADDITIONAL;
    }

    //Generate Loan EMIs
    public static function generateLoanEmi($loan_id, $amount, $duration) {
        try {
            $rate    = env('LOAN_EMI_RATE');
            $pending = $amount;
            $emiAmount = 0;
            $emiObject = [
                'loan_id'       => $loan_id,
                'emi_amount'    => 0,
                'emi_intrest'   => 0,
                'emi_principal' => round($amount/$duration,2),
                'emi_due_date'  => 0,
                'created_at'    => time(),
                'updated_at'    => time()
            ];
            $emi_arr = [];
            $weekDay = 7;
            //Generate EMi Amount from member function
            $emiAmount = self::calculatorEmi($amount, $rate, $duration);
            for($i=1;$i<=$duration;$i++) {
                $emi_arr[$i] = $emiObject;
                $emi_arr[$i]['emi_due_date'] = strtotime("+".($weekDay * $i)." days");
                $emi_arr[$i]['emi_intrest'] = round((($pending * $rate * $weekDay) / (365 * 100)), 2);//365 = 1 Year
                $emi_arr[$i]['emi_amount']  = $emiAmount;
                $pending-=$emi_arr[$i]['emi_principal'];
                $emi_arr[$i]['emi_principal'] = $pending;
            }
            self::insert($emi_arr);
            return true;
        } catch (\Exception $ex) {
            return false;
        }
    }

    //fetch EMI Amount
    public static function calculatorEmi($p, $r, $t) { 
        $emi; 
        // one month interest 
        $r = $r / (52 * 100);//52 as Weekly payment intrest counting(1 Year = 52 Week)
        // one month period
        $emi = ($p * $r * pow(1 + $r, $t)) /  
                      (pow(1 + $r, $t) - 1); 
        return ($emi); 
    } 
}