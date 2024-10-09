<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;


use App\Models\Auth;
use App\Models\Department;
use App\Models\Designation;
use App\Models\Employee;
use App\Models\Payroll;
use App\Models\Payslip;
//use App\Models\Loan;
use App\Models\PayslipEarnings;
use App\Models\PayslipDeductions;
/**
 * Class BaseController
 *
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 * Extend this class in any new controllers:
 *     class Home extends BaseController
 *
 * For security be sure to declare any new methods as protected or private.
 */
// abstract class BaseController extends Controller
class BaseController extends Controller
{
    /**
     * Instance of the main Request object.
     *
     * @var CLIRequest|IncomingRequest
     */
    protected $request;

    /**
     * An array of helpers to be loaded automatically upon
     * class instantiation. These helpers will be available
     * to all other controllers that extend BaseController.
     *
     * @var array
     */
    protected $helpers = [];

    /**
     * Constructor.
     */
    //protected $request;
    public function __construct()
    {
        $this->request = \Config\Services::request();
        $this->session = session();
        $this->auth_model = new Auth;
        $this->dept_model = new Department;
        $this->desg_model = new Designation;
        $this->emp_model = new Employee;
        $this->payroll_model = new Payroll;
        $this->payslip_model = new Payslip;
        $this->payslip_earn_model = new PayslipEarnings;
        $this->payslip_ded_model = new PayslipDeductions;
        //$this->loan_model = new Loan;
        $this->data = ['session' => $this->session,'request'=>$this->request];
    }


    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // Do Not Edit This Line
        parent::initController($request, $response, $logger);

        // Preload any models, libraries, etc, here.

        // E.g.: $this->session = \Config\Services::session();
    }
    public function payslip_view_base_con($id= null){
//var_dump($id);die();
//$id = 32;
if(empty($id)){
    $this->session->setFlashdata('main_error', "Payslip ID is unknown.");
    return redirect()->to('Main/payslips');
}
$this->data['page_title'] = "Payslip Details";
$details = $this->payslip_model
    ->select("payslips.*, 
              CONCAT(employees.last_name, ',', employees.first_name, COALESCE(CONCAT(' ', employees.middle_name), '')) as `name`, 
              employees.code, 
              CONCAT(departments.code, ' - ', departments.name) as department, 
              CONCAT(designations.code, ' - ', designations.name) as designation, 
              payrolls.code as payroll_code, 
              payrolls.title as payroll_name, 
              payslips.present, 
              payslips.late_undertime,
              payslips.salary,")
    ->join('payrolls', "payslips.payroll_id = payrolls.id", 'inner')
    ->join('employees', "payslips.employee_id = employees.id", 'inner')
    ->join('departments', "employees.department_id = departments.id", 'inner')
    ->join('designations', "employees.designation_id = designations.id", 'inner')
    //->join('loans', "employees.id = loans.employee_id", 'left')
    ->where("payslips.id = $id")
    ->first();

   

// Calculating totals
$salary = $details['salary'];
$present = $details['present'];
// $ot_rate = $details['ot_rate'];
// $ot_hr = $details['ot_hr'];
// $leg_rate = $details['leg_rate'];
// $leg_hr = $details['leg_hr'];
// $sp_rate = $details['sp_rate'];
//$sp_hr = $details['sp_hr'];

$daily = $salary / 6; // Assuming 6 working days
$hourly = $daily / 8;

$details['total_present'] = $hourly * $present;
// $details['total_overtime'] = $ot_rate * $ot_hr;
// $details['total_legal'] = $leg_rate * $leg_hr;
// $details['total_special'] = $sp_rate * $sp_hr;

$this->data['details'] = $details;
$this->data['earnings'] = $this->payslip_earn_model->where('payslip_id', $id)->findAll();
$this->data['deductions'] = $this->payslip_ded_model->where('payslip_id', $id)->findAll();

// return view('pages/payslips/view', $this->data);
// var_dump("testing");die();
// var_dump($this->data['deductions']);die();
return $this->data;
    }
}
