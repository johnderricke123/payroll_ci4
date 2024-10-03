<?php

namespace App\Controllers;
use App\Models\Auth;
use App\Models\Department;
use App\Models\Designation;
use App\Models\Employee;
use App\Models\Payroll;
use App\Models\Payslip;
use App\Models\PayslipEarnings;
use App\Models\PayslipDeductions;
use DateTime;

class Main extends BaseController
{   
    protected $request;

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
        $this->payslip_model = new Payslip;
        $this->data = ['session' => $this->session,'request'=>$this->request];
        
    }

    public function index()
    {
        $this->data['page_title']="Home";
        $this->data['departments']=$this->dept_model->countAll();
        $this->data['designations']=$this->desg_model->countAll();
        $this->data['employees']=$this->emp_model->countAll();
        $this->data['payrolls']=$this->payroll_model->countAll();
        return view('pages/home', $this->data);
    }

    public function users(){
        $this->data['page_title']="Users";
        $this->data['page'] =  !empty($this->request->getVar('page')) ? $this->request->getVar('page') : 1;
        $this->data['perPage'] =  10;
        $this->data['total'] =  $this->auth_model->where("id != '{$this->session->login_id}'")->countAllResults();
        $this->data['users'] = $this->auth_model->where("id != '{$this->session->login_id}'")->paginate($this->data['perPage']);
        $this->data['total_res'] = is_array($this->data['users'])? count($this->data['users']) : 0;
        $this->data['pager'] = $this->auth_model->pager;
        return view('pages/users/list', $this->data);
    }
    public function user_add(){
        if($this->request->getMethod() == 'post'){
            extract($this->request->getPost());
            if($password !== $cpassword){
                $this->session->setFlashdata('error',"Password does not match.");
            }else{
                $udata= [];
                $udata['name'] = $name;
                $udata['email'] = $email;
                if(!empty($password))
                $udata['password'] = password_hash($password, PASSWORD_DEFAULT);
                $checkMail = $this->auth_model->where('email',$email)->countAllResults();
                if($checkMail > 0){
                    $this->session->setFlashdata('error',"User Email Already Taken.");
                }else{
                    $save = $this->auth_model->save($udata);
                    if($save){
                        $this->session->setFlashdata('main_success',"User Details has been updated successfully.");
                        return redirect()->to('Main/users');
                    }else{
                        $this->session->setFlashdata('error',"User Details has failed to update.");
                    }
                }
            }
        }

        $this->data['page_title']="Add User";
        return view('pages/users/add', $this->data);
    }
    public function user_edit($id=''){
        if(empty($id))
        return redirect()->to('Main/users');
        if($this->request->getMethod() == 'post'){
            extract($this->request->getPost());
            if($password !== $cpassword){
                $this->session->setFlashdata('error',"Password does not match.");
            }else{
                $udata= [];
                $udata['name'] = $name;
                $udata['email'] = $email;
                if(!empty($password))
                $udata['password'] = password_hash($password, PASSWORD_DEFAULT);
                $checkMail = $this->auth_model->where('email',$email)->where('id!=',$id)->countAllResults();
                if($checkMail > 0){
                    $this->session->setFlashdata('error',"User Email Already Taken.");
                }else{
                    $update = $this->auth_model->where('id',$id)->set($udata)->update();
                    if($update){
                        $this->session->setFlashdata('success',"User Details has been updated successfully.");
                        return redirect()->to('Main/user_edit/'.$id);
                    }else{
                        $this->session->setFlashdata('error',"User Details has failed to update.");
                    }
                }
            }
        }

        $this->data['page_title']="Edit User";
        $this->data['user'] = $this->auth_model->where("id ='{$id}'")->first();
        return view('pages/users/edit', $this->data);
    }
    
    public function user_delete($id=''){
        if(empty($id)){
                $this->session->setFlashdata('main_error',"user Deletion failed due to unknown ID.");
                return redirect()->to('Main/users');
        }
        $delete = $this->auth_model->where('id', $id)->delete();
        if($delete){
            $this->session->setFlashdata('main_success',"User has been deleted successfully.");
        }else{
            $this->session->setFlashdata('main_error',"user Deletion failed due to unknown ID.");
        }
        return redirect()->to('Main/users');
    }


    // Department
    public function departments(){
        $this->data['page_title']="Departments";
        $this->data['page'] =  !empty($this->request->getVar('page')) ? $this->request->getVar('page') : 1;
        $this->data['perPage'] =  10;
        $this->data['total'] =  $this->dept_model->countAllResults();
        $this->data['departments'] = $this->dept_model->paginate($this->data['perPage']);
        $this->data['total_res'] = is_array($this->data['departments'])? count($this->data['departments']) : 0;
        $this->data['pager'] = $this->dept_model->pager;
        return view('pages/departments/list', $this->data);
    }
    public function department_add(){
        if($this->request->getMethod() == 'post'){
            extract($this->request->getPost());
            $udata= [];
            $udata['code'] = $code;
            $udata['name'] = $name;
            $checkCode = $this->dept_model->where('code',$code)->countAllResults();
            if($checkCode){
                $this->session->setFlashdata('error',"Department Code Already Taken.");
            }else{
                $save = $this->dept_model->save($udata);
                if($save){
                    $this->session->setFlashdata('main_success',"Department Details has been updated successfully.");
                    return redirect()->to('Main/departments/');
                }else{
                    $this->session->setFlashdata('error',"Department Details has failed to update.");
                }
            }
        }

        $this->data['page_title']="Add New Department";
        return view('pages/departments/add', $this->data);
    }
    public function department_edit($id=''){
        if(empty($id))
        return redirect()->to('Main/departments');
        if($this->request->getMethod() == 'post'){
            extract($this->request->getPost());
            $udata= [];
            $udata['code'] = $code;
            $udata['name'] = $name;
            $checkCode = $this->dept_model->where('code',$code)->where("id!= '{$id}'")->countAllResults();
            if($checkCode){
                $this->session->setFlashdata('error',"Department Code Already Taken.");
            }else{
                $update = $this->dept_model->where('id',$id)->set($udata)->update();
                if($update){
                    $this->session->setFlashdata('success',"Department Details has been updated successfully.");
                    return redirect()->to('Main/department_edit/'.$id);
                }else{
                    $this->session->setFlashdata('error',"Department Details has failed to update.");
                }
            }
        }

        $this->data['page_title']="Edit Department";
        $this->data['department'] = $this->dept_model->where("id ='{$id}'")->first();
        return view('pages/departments/edit', $this->data);
    }
    
    public function department_delete($id=''){
        if(empty($id)){
                $this->session->setFlashdata('main_error',"Department Deletion failed due to unknown ID.");
                return redirect()->to('Main/departments');
        }
        $delete = $this->dept_model->where('id', $id)->delete();
        if($delete){
            $this->session->setFlashdata('main_success',"Department has been deleted successfully.");
        }else{
            $this->session->setFlashdata('main_error',"Department Deletion failed due to unknown ID.");
        }
        return redirect()->to('Main/departments');
    }

    // Designation
    public function designations(){
        $this->data['page_title']="Designations";
        $this->data['page'] =  !empty($this->request->getVar('page')) ? $this->request->getVar('page') : 1;
        $this->data['perPage'] =  10;
        $this->data['total'] =  $this->desg_model->countAllResults();
        $this->data['designations'] = $this->desg_model->paginate($this->data['perPage']);
        $this->data['total_res'] = is_array($this->data['designations'])? count($this->data['designations']) : 0;
        $this->data['pager'] = $this->desg_model->pager;
        return view('pages/designations/list', $this->data);
    }
    public function designation_add(){
        if($this->request->getMethod() == 'post'){
            extract($this->request->getPost());
            $udata= [];
            $udata['code'] = $code;
            $udata['name'] = $name;
            $checkCode = $this->desg_model->where('code',$code)->countAllResults();
            if($checkCode){
                $this->session->setFlashdata('error',"Designation Code Already Taken.");
            }else{
                $save = $this->desg_model->save($udata);
                if($save){
                    $this->session->setFlashdata('main_success',"Designation Details has been updated successfully.");
                    return redirect()->to('Main/designations/');
                }else{
                    $this->session->setFlashdata('error',"Designation Details has failed to update.");
                }
            }
        }

        $this->data['page_title']="Add New Designation";
        return view('pages/designations/add', $this->data);
    }
    public function designation_edit($id=''){
        if(empty($id))
        return redirect()->to('Main/designations');
        if($this->request->getMethod() == 'post'){
            extract($this->request->getPost());
            $udata= [];
            $udata['code'] = $code;
            $udata['name'] = $name;
            $checkCode = $this->desg_model->where('code',$code)->where("id!= '{$id}'")->countAllResults();
            if($checkCode){
                $this->session->setFlashdata('error',"Designation Code Already Taken.");
            }else{
                $update = $this->desg_model->where('id',$id)->set($udata)->update();
                if($update){
                    $this->session->setFlashdata('success',"Designation Details has been updated successfully.");
                    return redirect()->to('Main/designation_edit/'.$id);
                }else{
                    $this->session->setFlashdata('error',"Designation Details has failed to update.");
                }
            }
        }

        $this->data['page_title']="Edit Designation";
        $this->data['designation'] = $this->desg_model->where("id ='{$id}'")->first();
        return view('pages/designations/edit', $this->data);
    }
    
    public function designation_delete($id=''){
        if(empty($id)){
                $this->session->setFlashdata('main_error',"Designation Deletion failed due to unknown ID.");
                return redirect()->to('Main/designations');
        }
        $delete = $this->desg_model->where('id', $id)->delete();
        if($delete){
            $this->session->setFlashdata('main_success',"Designation has been deleted successfully.");
        }else{
            $this->session->setFlashdata('main_error',"Designation Deletion failed due to unknown ID.");
        }
        return redirect()->to('Main/designations');
    }

    
    // employee
    public function employees(){
        $this->data['page_title']="Employees";
        $this->data['page'] =  !empty($this->request->getVar('page')) ? $this->request->getVar('page') : 1;
        $this->data['perPage'] =  10;
        if(!empty($this->request->getVar('search'))){
            $search = $this->request->getVar('search');
            $this->emp_model->where(" employees.`code` like '%{$search}%' or employees.`first_name` like '%{$search}%' or employees.`middle_name` like '%{$search}%' or employees.`last_name` like '%{$search}%' or CONCAT(employees.last_name, ',', employees.first_name, COALESCE(CONCAT(' ', employees.middle_name), '')) like '%{$search}%'");
        }
        $this->data['total'] =  $this->emp_model->countAllResults();
        if(!empty($this->request->getVar('search'))){
            $search = $this->request->getVar('search');
            $this->emp_model->where(" employees.`code` like '%{$search}%' or employees.`first_name` like '%{$search}%' or employees.`middle_name` like '%{$search}%' or employees.`last_name` like '%{$search}%' or CONCAT(employees.last_name, ',', employees.first_name, COALESCE(CONCAT(' ', employees.middle_name), '')) like '%{$search}%'");
        }
        $this->data['employees'] = $this->emp_model
                                    ->select("employees.*, CONCAT(employees.last_name, ',', employees.first_name, COALESCE(CONCAT(' ', employees.middle_name), '')) as `name`, CONCAT(departments.code,' - ', departments.name) as department, CONCAT(designations.code,' - ', designations.name) as designation")
                                    ->join('departments'," employees.department_id = departments.id ",'inner')
                                    ->join('designations'," employees.designation_id = designations.id ",'inner')
                                    ->paginate($this->data['perPage']);
        $this->data['total_res'] = is_array($this->data['employees'])? count($this->data['employees']) : 0;
        $this->data['pager'] = $this->emp_model->pager;
        return view('pages/employees/list', $this->data);
    }
    public function employee_add(){
        $this->data['departments'] = $this->dept_model->findAll();
        $this->data['designations'] = $this->desg_model->findAll();
        if($this->request->getMethod() == 'post'){
            extract($this->request->getPost());
            $udata= [];
            $udata['code'] = $code;
            $udata['first_name'] = $first_name;
            $udata['middle_name'] = $middle_name;
            $udata['last_name'] = $last_name;
            $udata['dob'] = $dob;
            $udata['gender'] = $gender;
            $udata['email'] = $email;
            $udata['date_hired'] = $date_hired;
            $udata['department_id'] = $department_id;
            $udata['designation_id'] = $designation_id;
            $udata['salary'] = $salary;
            $udata['status'] = $status;
            $checkCode = $this->emp_model->where('code',$code)->countAllResults();
            if($checkCode){
                $this->session->setFlashdata('error',"Employee Code Already Taken.");
            }else{
                $save = $this->emp_model->save($udata);
                if($save){
                    $id = $this->emp_model->insertID();
                    $this->session->setFlashdata('main_success',"Employee Details has been updated successfully.");
                    return redirect()->to('Main/employee_view/'.$id);
                }else{
                    $this->session->setFlashdata('error',"Employee Details has failed to update.");
                }
            }
        }

        $this->data['page_title']="Add New Employee";
        return view('pages/employees/add', $this->data);
    }
    public function employee_edit($id=''){
        $this->data['departments'] = $this->dept_model->findAll();
        $this->data['designations'] = $this->desg_model->findAll();
        if(empty($id))
        return redirect()->to('Main/employees');
        if($this->request->getMethod() == 'post'){
            extract($this->request->getPost());
            $udata= [];
            $udata['code'] = $code;
            $udata['first_name'] = $first_name;
            $udata['middle_name'] = $middle_name;
            $udata['last_name'] = $last_name;
            $udata['dob'] = $dob;
            $udata['gender'] = $gender;
            $udata['email'] = $email;
            $udata['date_hired'] = $date_hired;
            $udata['department_id'] = $department_id;
            $udata['designation_id'] = $designation_id;
            $udata['salary'] = $salary;
            $udata['status'] = $status;
            $checkCode = $this->emp_model->where('code',$code)->where("id!= '{$id}'")->countAllResults();
            if($checkCode){
                $this->session->setFlashdata('error',"Employee Code Already Taken.");
            }else{
                $update = $this->emp_model->where('id',$id)->set($udata)->update();
                if($update){
                    $this->session->setFlashdata('main_success',"Employee Details has been updated successfully.");
                    return redirect()->to('Main/employee_view/'.$id);
                }else{
                    $this->session->setFlashdata('error',"Employee Details has failed to update.");
                }
            }
        }

        $this->data['page_title']="Edit Employee";
        $this->data['employee'] = $this->emp_model->where("id ='{$id}'")->first();
        return view('pages/employees/edit', $this->data);
    }
    
    public function employee_delete($id=''){
        if(empty($id)){
                $this->session->setFlashdata('main_error',"Employee Deletion failed due to unknown ID.");
                return redirect()->to('Main/employees');
        }
        $delete = $this->emp_model->where('id', $id)->delete();
        if($delete){
            $this->session->setFlashdata('main_success',"Employee has been deleted successfully.");
        }else{
            $this->session->setFlashdata('main_error',"Employee Deletion failed due to unknown ID.");
        }
        return redirect()->to('Main/employees');
    }

    public function employee_view($id=''){
        if(empty($id)){
            $this->session->setFlashdata('main_error',"Employee ID is unknown.");
            return redirect()->to('Main/employees');
        }
        $this->data['page_title']="Employee Details";
        $this->data['details'] = $this->emp_model
                                    ->select("employees.*, CONCAT(employees.last_name, ',', employees.first_name, COALESCE(CONCAT(' ', employees.middle_name), '')) as `name`, CONCAT(departments.code,' - ', departments.name) as department, CONCAT(designations.code,' - ', designations.name) as designation")
                                    ->join('departments'," employees.department_id = departments.id ",'inner')
                                    ->join('designations'," employees.designation_id = designations.id ",'inner')
                                    ->where("employees.id = $id")->first();
        return view('pages/employees/view', $this->data);
        
    }

    // Payroll
    public function payrolls(){
        $this->data['page_title']="Payrolls";
        $this->data['page'] =  !empty($this->request->getVar('page')) ? $this->request->getVar('page') : 1;
        $this->data['perPage'] =  10;
        $this->data['total'] =  $this->payroll_model->countAllResults();
        $this->data['payrolls'] = $this->payroll_model->paginate($this->data['perPage']);
        $this->data['total_res'] = is_array($this->data['payrolls'])? count($this->data['payrolls']) : 0;
        $this->data['pager'] = $this->payroll_model->pager;
        return view('pages/payrolls/list', $this->data);
    }
    public function payroll_add(){
        if($this->request->getMethod() == 'post'){
            extract($this->request->getPost());
            $udata= [];
            $udata['code'] = $code;
            $udata['title'] = $title;
            $udata['from_date'] = $from_date;
            $udata['to_date'] = $to_date;
            $checkCode = $this->payroll_model->where('code',$code)->countAllResults();
            if($checkCode){
                $this->session->setFlashdata('error',"Payroll Code Already Taken.");
            }else{
                $save = $this->payroll_model->save($udata);
                if($save){
                    $this->session->setFlashdata('main_success',"Payroll Details has been updated successfully.");
                    return redirect()->to('Main/payrolls/');
                }else{
                    $this->session->setFlashdata('error',"Payroll Details has failed to update.");
                }
            }
        }

        $this->data['page_title']="Add New Payroll";
        return view('pages/payrolls/add', $this->data);
    }
    public function payroll_edit($id=''){
        if(empty($id))
        return redirect()->to('Main/payrolls');
        if($this->request->getMethod() == 'post'){
            extract($this->request->getPost());
            $udata= [];
            $udata['code'] = $code;
            $udata['title'] = $title;
            $udata['from_date'] = $from_date;
            $udata['to_date'] = $to_date;
            $checkCode = $this->payroll_model->where('code',$code)->where("id!= '{$id}'")->countAllResults();
            if($checkCode){
                $this->session->setFlashdata('error',"Payroll Code Already Taken.");
            }else{
                $update = $this->payroll_model->where('id',$id)->set($udata)->update();
                if($update){
                    $this->session->setFlashdata('success',"Payroll Details has been updated successfully.");
                    return redirect()->to('Main/payroll_edit/'.$id);
                }else{
                    $this->session->setFlashdata('error',"Payroll Details has failed to update.");
                }
            }
        }

        $this->data['page_title']="Edit Payroll";
        $this->data['payroll'] = $this->payroll_model->where("id ='{$id}'")->first();
        return view('pages/payrolls/edit', $this->data);
    }
    
    public function payroll_delete($id=''){
        if(empty($id)){
                $this->session->setFlashdata('main_error',"Payroll Deletion failed due to unknown ID.");
                return redirect()->to('Main/payrolls');
        }
        $delete = $this->payroll_model->where('id', $id)->delete();
        if($delete){
            $this->session->setFlashdata('main_success',"Payroll has been deleted successfully.");
        }else{
            $this->session->setFlashdata('main_error',"Payroll Deletion failed due to unknown ID.");
        }
        return redirect()->to('Main/payrolls');
    }

    // payslip
    public function payslips(){
        $this->data['page_title']="Payslips";
        $this->data['page'] =  !empty($this->request->getVar('page')) ? $this->request->getVar('page') : 1;
        $this->data['perPage'] =  10;
        $this->data['total'] =  $this->payslip_model->countAllResults();
        $this->data['payslips'] = $this->payslip_model
                                        ->select("`payslips`.*, CONCAT(employees.last_name, ',', employees.first_name, COALESCE(CONCAT(' ', employees.middle_name), '')) as `employee_name`, employees.code as employee_code, payrolls.code as payroll_code, payrolls.title as payroll_name")
                                        ->join('payrolls'," payslips.payroll_id = payrolls.id ",'inner')
                                        ->join('employees'," payslips.employee_id = employees.id ",'inner')
                                        ->paginate($this->data['perPage']);
        $this->data['total_res'] = is_array($this->data['payslips'])? count($this->data['payslips']) : 0;
        $this->data['pager'] = $this->payslip_model->pager;
        return view('pages/payslips/list', $this->data);
    }
    public function payslip_add(){
        if($this->request->getMethod() == 'post'){
            extract($this->request->getPost());
            $udata= [];
            foreach($this->request->getPost() as $k => $v){
                if(!is_array($this->request->getPost($k)))
                $udata[$k] = $v;
            }
            $checkCode = $this->payslip_model->where('employee_id',$employee_id)->where('payroll_id',$payroll_id)->countAllResults();
            if($checkCode){
                $this->session->setFlashdata('main_error',"Employee has Already a Payslip Record on the selected Payroll.");
            }else{
                $save = $this->payslip_model->save($udata);
                if($save){
                    $payslip_id = $this->payslip_model->insertID();
                    $udata= [];
                    foreach($earning_name as $k => $v){
                        if(!empty($v) && !empty($earning_amount[$k])){
                            $udata['payslip_id'] = $payslip_id;
                            $udata['name'] = $v;
                            $udata['amount'] = $earning_amount[$k];
                        }
                        $this->payslip_earn_model->save($udata);
                    }
                    $udata= [];
                    foreach($deduction_name as $k => $v){
                        if(!empty($v) && !empty($deduction_amount[$k])){
                            $udata['payslip_id'] = $payslip_id;
                            $udata['name'] = $v;
                            $udata['amount'] = $deduction_amount[$k];
                        }
                        $this->payslip_ded_model->save($udata);
                    }

                    $this->session->setFlashdata('main_success',"Employee's Payslip has been updated successfully.");
                    return redirect()->to('Main/payslips/');
                }else{
                    $this->session->setFlashdata('main_error',"Payslip Details has failed to update.");
                }
            }
        }
        $this->data['employees'] = $this->emp_model->select("*, CONCAT(employees.last_name, ',', employees.first_name, COALESCE(CONCAT(' ', employees.middle_name), '')) as `name`")->findAll();
        $this->data['payrolls'] = $this->payroll_model->findAll();
        $this->data['page_title']="Add New Payslip";
        return view('pages/payslips/add', $this->data);
    }
    public function payslip_delete($id=''){
        if(empty($id)){
                $this->session->setFlashdata('main_error',"Payslip Deletion failed due to unknown ID.");
                return redirect()->to('Main/payslips');
        }
        $delete = $this->payslip_model->where('id', $id)->delete();
        if($delete){
            $this->session->setFlashdata('main_success',"Payslip has been deleted successfully.");
        }else{
            $this->session->setFlashdata('main_error',"Payslip Deletion failed due to unknown ID.");
        }
        return redirect()->to('Main/payslips');
    }
    public function payslip_view($id=''){
        if(empty($id)){
            $this->session->setFlashdata('main_error',"payslip ID is unknown.");
            return redirect()->to('Main/payslips');
        }
        $this->data['page_title']="payslip Details";
        $this->data['details'] = $this->payslip_model
                                    ->select("payslips.*, CONCAT(employees.last_name, ',', employees.first_name, COALESCE(CONCAT(' ', employees.middle_name), '')) as `name`, employees.code, CONCAT(departments.code,' - ', departments.name) as department, CONCAT(designations.code,' - ', designations.name) as designation, payrolls.code as payroll_code, payrolls.title as payroll_name")
                                    ->join('payrolls'," payslips.payroll_id = payrolls.id ",'inner')
                                    ->join('employees'," payslips.employee_id = employees.id ",'inner')
                                    ->join('departments'," employees.department_id = departments.id ",'inner')
                                    ->join('designations'," employees.designation_id = designations.id ",'inner')
                                    ->where("payslips.id = $id")->first();
        $this->data['earnings'] = $this->payslip_earn_model->where('payslip_id', $id)->findAll();
        $this->data['deductions'] = $this->payslip_ded_model->where('payslip_id', $id)->findAll();
        return view('pages/payslips/view', $this->data);
    }
// *********************************************************************
    public function get_data_from_sentry(){
        helper('time');
        //echo json_encode($this->request->getVar('empid'));die();

        $emp_code = $this->request->getVar('empid');
        $StartDate = $this->request->getVar('StartDate');
        $EndDate = $this->request->getVar('EndDate'); 

        // $emp_code = "5";
        // $StartDate = "2024-09-05";
        // $EndDate = "2024-09-07"; 

        

        //echo json_encode("//emp_code=$emp_code//StartDate=$StartDate//EndDate=$EndDate//");die();
// *********************************FETCHING API TOKEN FROM SENTRY*********************************
$curl = curl_init();

curl_setopt_array($curl, array(
//   CURLOPT_URL => 'https://obliging-sparrow-just.ngrok-free.app/api/Integration/GetToken?username=1010&password=123123123',
  CURLOPT_URL => 'https://more-macaw-star.ngrok-free.app/api/Integration/GetToken?username=1010&password=123123123',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'GET',
));

$response = curl_exec($curl);

curl_close($curl);
//echo $response;die();
// *********************************FETCHING API TOKEN FROM SENTRY*********************************
// *********************************API FETCHING TIME LOG DATA FROM SENTRY*********************************

    $curl = curl_init();

    curl_setopt_array($curl, array(
      //CURLOPT_URL => 'https://obliging-sparrow-just.ngrok-free.app/api/Integration/GetTimeLogsBulkData',
      CURLOPT_URL => 'https://more-macaw-star.ngrok-free.app/api/api/Integration/GetTimeLogsBulkData',
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'POST',
      CURLOPT_POSTFIELDS =>'{
            "PersonnelNo": "'.$emp_code.'",
            "StartDate": "'.$StartDate.'",
            "EndDate": "'.$EndDate.'",
            "start": 0, "max": 100
        }',
      CURLOPT_HTTPHEADER => array(
        'Content-Type: application/json',
        'Authorization: Bearer '.$response.''
      ),
    ));

    $response = curl_exec($curl);

    curl_close($curl);

    $dataArray = json_decode($response, true);
   //echo json_encode($dataArray);die();
   if(!$dataArray){
        //echo json_encode("No Data Found!");
        exit();
   }
    //$i = 0;
    $data = array();

    foreach($dataArray as $logs){
        $data[$logs['logdate']][$logs['inout']] = $logs['logdate']." ".$logs['logtime'].":00";    
        //$data[$logs['logdate']][$logs['inout']] = $logs['logdate']." ".$logs['logtime'].":00";
        
    }

//echo json_encode($data);die();
    // Input Array
    //$arr = ["2016-09-12 00:00:00", "2009-09-09 01:00:00", "2009-09-09 04:00:00", "2009-09-09 02:00:00", "2009-09-09 03:00:00"];
    
    //$data['2024-09-05']
    //usort($arr, "compareByTimeStamp");
//print_r($data);die();


// foreach ($data as $key => $field) {    
    
//     usort($data[$key], 
//     function ($time1, $time2)
//     {
        
//         if (strtotime($time1) < strtotime($time2)) {
//             return 1;
//         } elseif (strtotime($time1) > strtotime($time2)) {
//             return -1;
//         } else {
//             return 0;
//         }
//     }
//     );
// }

//print_r($data);die();

foreach ($data as $key => $field) {    
    uasort($data[$key], function ($time1, $time2) {
        // $time1 = explode("|", $time1);
        // $time2 = explode("|", $time2);
        
        if (strtotime($time1) < strtotime($time2)) {
            return 1;
        } elseif (strtotime($time1) > strtotime($time2)) {
            return -1;
        } else {
            return 0;
        }
    });
}


// **************************NEED NALANG MA COMPUTE AND TIME DIFFERENCE FOR EVERYDAY TIME LOGS******************************
//print_r($data);die();

foreach($data as $key => $field){
 //print_r($key);die();
// continue;

    // $datetime_1 = '2022-04-10 11:15:30'; 
    // $datetime_2 = '2022-04-12 11:20:45';

//     if(array_key_exists("OUT",$data[$key])){
// var_dump("true");die();
//     } else{
//         var_dump("false");die();
//     }

 
    $IN = $data && array_key_exists("IN", $data[$key]) ? $field['IN'] : null;
    $OVERTIMEOUT = $data && array_key_exists("OVERTIME OUT", $data[$key]) ? $field['OVERTIME OUT'] : null;
    $OVERTIMEIN = $data && array_key_exists("OVERTIME IN", $data[$key]) ? $field['OVERTIME IN'] : null;
    $OUT = $data && array_key_exists("OUT", $data[$key]) ? $field['OUT'] : null;

    // var_dump($IN);
    // die();

    // $IN = $field['IN']; 
    // $OVERTIMEOUT = $field['OVERTIME OUT'];
    // $OVERTIMEIN = $field['OVERTIME IN'];
    // $OUT = $field['OUT'];



    // $start_datetime = new DateTime($IN); 
    // $diff = $start_datetime->diff(new DateTime($OVERTIMEOUT)); 

    // $end_datetime = new DateTime($OVERTIMEIN); 
    // $diff2 = $end_datetime->diff(new DateTime($OUT)); 
    
    // echo $diff->h.' Hours<br>'; 
    // echo $diff->i.' Minutes<br>'; 
    // echo "<br>//////////////////////////<br>"; 
    // echo $diff2->h.' Hours<br>'; 
    // echo $diff2->i.' Minutes<br>'; 

// $time = $diff->h.":".$diff->i.":"."00";
// $time2 = $diff2->h.":".$diff2->i.":"."00";

// $secs = strtotime($time2)-strtotime("00:00:00");
// $result = date("H:i:s",strtotime($time)+$secs);
//var_dump($IN."//".$OVERTIMEOUT."//".$OVERTIMEIN."//".$OUT);die();
$result = time_add($IN, $OVERTIMEOUT, $OVERTIMEIN, $OUT);
$array_duties[] = $result; 
}

//**********************RETREIVE OVERTIME HOURS**********************
// foreach($array_duties as $overtimes){
//     $time_rendered = $overtimes;
//     $regular_duty_time = "08:00:00";
    
//     if($time_rendered > $regular_duty_time){
//         $end_datetime = new DateTime($time_rendered); 
//         $diff2 = $end_datetime->diff(new DateTime($regular_duty_time));         
//         $time2 = $diff2->h.":".$diff2->i.":"."00";
//         $overtime_rendered[] = $time2; 
//     }
// }

//$overtime_rendered = retreive_overtime($array_duties);
//print_r($array_duties);die();
    foreach($array_duties as $overtimes){
        $time_rendered = $overtimes;
        $regular_duty_time = "08:00:00";
        
        if($time_rendered > $regular_duty_time){
            $end_datetime = new DateTime($time_rendered); 
            $diff2 = $end_datetime->diff(new DateTime($regular_duty_time));         
            $time2 = $diff2->h.":".$diff2->i.":"."00";
            // return $overtime_rendered[] = $time2;
            $overtime_rendered[] = $time2; 
        }
    }
//print_r("testing");die();
//print_r($overtime_rendered);die();
// *********************************IPA REFLECT ANF OVERTIME TOTAL SA USER INTERFACE*********************************
$overtime_total = time_all_add($overtime_rendered);
// *********************************IPA REFLECT ANF OVERTIME TOTAL SA USER INTERFACE*********************************
//print_r($array_duties);die();
//**********************RETREIVE OVERTIME HOURS**********************
$result = time_all_add($array_duties);
// print_r($overtime_total."<br>");
//print_r($result);die();

// $ot_total_arr[0] = $overtime_total;
// $ot_total_arr[1] = $result;
//$test = time_deducts($ot_total_arr);

$time_explode = explode(":",$result);
$converted = $time_explode[0] + ($time_explode[1] / 60) + ($time_explode[2] / 3600);

$ot_time_explode = explode(":",$overtime_total);
$ot_converted = $ot_time_explode[0] + ($ot_time_explode[1] / 60) + ($ot_time_explode[2] / 3600);
// echo json_encode($converted."//".$ot_converted);
$regular_rendered_time = $converted - $ot_converted;
//die();
$computed_data[0] =$regular_rendered_time;
$computed_data[1] =$ot_converted;
echo json_encode($computed_data);
// $data[] =$converted;
// $data[] =$overtime_total;
// echo json_encode($data);


// *********************************API FETCHING DATA FROM SENTRY*********************************
    }



// *******************************************************************************
}
