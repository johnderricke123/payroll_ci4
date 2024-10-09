<?= $this->extend('layouts/main') ?>

<?= $this->section('custom_css') ?>
<style>
    div#product-list {
        height: 25em;
        overflow: auto;
    }
</style>
<?= $this->endSection() ?>
<?= $this->section('content') ?>
<div class="card rounded-0">
    <div class="card-header">
        <div class="d-flex w-100 justify-content-between">
            <div class="card-title h4 mb-0 fw-bolder">New Payslip</div>
        </div>
    </div>
    <div class="card-body">
        <div class="container-fluid">
            <form action="<?= base_url("Main/payslip_add") ?>" id="payslip-form" method="POST" onkeydown="return event.key != 'Enter';">
                <input type="hidden" name="salary" value="0">
                <fieldset class="border py-3 rounded-0 mb-3">
                    <div class="container-fluid">
                        <div class="row align-items-end">
                            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 mb-3">
                                <input type="hidden" name="date_from" id="date_from" value="" />
                                <input type="hidden" name="date_to" id="date_to" value="" />

                                <label for="payroll_id" class="control-label">Payroll</label>
                                <select id="payroll_id" name="payroll_id" class="form-select rounded-0">
                                    <option value="" disabled selected></option>
                                    <?php
                                    foreach ($payrolls as $row):
                                    ?>
                                        <option value="<?= $row['id'] ?>" data-from="<?php echo $row['from_date'] ?>" data-to="<?php echo $row['to_date'] ?>"><?= $row['code'] . " - " . $row['title'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 mb-3">
                                <label for="employee_id" class="control-label">Employee</label>
                                <select id="employee_id" name="employee_id" class="form-select rounded-0" disabled>
                                    <option value="" disabled selected></option>
                                    <?php
                                    foreach ($employees as $row):
                                    ?>
                                        <option value="<?= $row['id'] ?>" data-salary="<?= $row['salary'] ?>" emp-code="<?php echo $row['code'] ?>"><?= $row['code'] . " - " . $row['name'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 mb-3">
                                <label for="present" class="control-label">Hours <sup>Rendered</sup></label>
                                <input type="number" class="form-control rounded-0" id="present" name="present" min="0" step="any" required>
                            </div>
                            <!-- ***********************************LATE/UNDERTIME*********************************** -->
                            <!-- <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 mb-3">
                                <label for="late_undertime" class="control-label">Late/Undertime <sup>mins</sup></label>
                                <input type="number" class="form-control rounded-0" id="late_undertime" name="late_undertime" min="0" step="any" required>
                            </div> -->
                            <!-- ***********************************LATE/UNDERTIME*********************************** -->
                            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 mb-3">
                                <label for="overtime_total" class="control-label">Overtime Total</label>
                                <input type="text" class="form-control rounded-0" id="overtime_total" value="" name="overtime">
                            </div>

                            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 mb-3">
                                <label for="overtime_total" class="control-label">Employee Salary Per Week</label>
                                <input type="text" class="form-control rounded-0" id="emp_salary" value="" name="emp_salary" readonly>
                            </div>

                        </div>
                    </div>
                </fieldset>
                <div class="row">
                    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                        <fieldset class="mb-3 py-3 border">
                            <legend class="w-auto px-3 mx-3">Earnings</legend>
                            <div class="container-fluid">
                                <table class="table table-bordered table-striped" id="earnings-table">
                                    <thead>
                                        <tr class="bg-dark bg-gradient bg-opacity-75 text-light">
                                            <th class="p-1 text-center"></th>
                                            <th class="p-1 text-center">Name</th>
                                            <th class="p-1 text-center">Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                                <div class="text-end">
                                    <button class="btn btn-light bg-gradient rounded-0 btn-sm border" id="add_earning" type="button"><i class="fa fa-plus"></i> Add Earning</button>
                                </div>
                            </div>
                        </fieldset>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                        <fieldset class="mb-3 py-3 border">
                            <legend class="w-auto px-3 mx-3">Deductions</legend>
                            <div class="container-fluid">
                                <table class="table table-bordered table-striped" id="deductions-table">
                                    <thead>
                                        <tr class="bg-dark bg-gradient bg-opacity-75 text-light">
                                            <th class="p-1 text-center"></th>
                                            <th class="p-1 text-center">Name</th>
                                            <th class="p-1 text-center">Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                                <div class="text-end">
                                    <button class="btn btn-light bg-gradient rounded-0 btn-sm border" id="add_deduction" type="button"><i class="fa fa-plus"></i> Add Deduction</button>
                                </div>
                            </div>
                        </fieldset>
                    </div>
                </div>
                <fieldset class="mb-3 border rounded-0 py-3 mb-3">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 mb-3">
                                <label for="witholding_tax" class="control-label">Witholding Tax</label>
                                <input type="number" class="form-control rounded-0 text-end" id="witholding_tax" name="witholding_tax" min="0" step="any" required>
                            </div>
                            <!-- <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 mb-3">
                                <label for="overtime_pay" class="control-label">Overtime Pay</label>
                                <input type="number" class="form-control rounded-0 text-end" id="overtime_pay" name="overtime_pay" min="0" step="any" required value="0" readonly>
                            </div> -->
                            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 mb-3">
                                <label for="net" class="control-label">Net</label>
                                <input type="number" class="form-control rounded-0 text-end" id="net" name="net" min="0" step="any" required value="0" readonly>
                            </div>
                        </div>
                    </div>
                </fieldset>
            </form>
        </div>
    </div>
    <div class="card-footer text-center">
        <button class="btn btn-primary rounded-0" id="save_payslip" type="button"><i class="fa fa-save"></i> Save Payslip</button>
    </div>
</div>
<noscript id="earning-clone">
    <tr>
        <td class="px-2 py-1 align-middle text-center">
            <button class="btn btn-outline-danger btn-sm rounded-0 rem-earning" type="button"><i class="fa fa-times"></i></button>
        </td>
        <td class="px-2 py-1 align-middle">
            <input type="text" class="form-control form-control-sm rounded-0" value="" name="earning_name[]">
        </td>
        <td class="px-2 py-1 align-middle">
            <input type="number" step="any" class="form-control form-control-sm rounded-0" name="earning_amount[]">
        </td>
    </tr>
</noscript>
<noscript id="deduction-clone">
    <tr>
        <td class="px-2 py-1 align-middle text-center">
            <button class="btn btn-outline-danger btn-sm rounded-0 rem-deduction" type="button"><i class="fa fa-times"></i></button>
        </td>
        <td class="px-2 py-1 align-middle">
            <input type="text" class="form-control form-control-sm rounded-0" value="" name="deduction_name[]">
        </td>
        <td class="px-2 py-1 align-middle">
            <input type="number" step="any" class="form-control form-control-sm rounded-0" name="deduction_amount[]">
        </td>
    </tr>
</noscript>
<?= $this->endSection() ?>
<?= $this->section('custom_js') ?>



<script>
    $(document).ready(function() {
        for (i = 0; i <= 3; i++) {
            var tr = $($('noscript#deduction-clone').html()).clone()
            $('#deductions-table tbody').append(tr)

            tr.find('.rem-deduction').click(function() {
                tr.remove()
                compute_total()
            })
            tr.find('[name="deduction_amount[]"]').on('change input', function() {
                compute_total()
            })
            //**********************************TEMPORARY: MUST BE DYNAMIC**********************************
            if (i == 0) {
                tr.find('[name="deduction_name[]"]').val('SSS');
            }
            if (i == 1) {
                tr.find('[name="deduction_name[]"]').val('PAG-IBIG');
            }
            if (i == 2) {
                tr.find('[name="deduction_name[]"]').val('PHILHEALTH');
            }
            if (i == 3) {
                tr.find('[name="deduction_name[]"]').val('Cash Advance');
            }
            //**********************************TEMPORARY: MUST BE DYNAMIC**********************************
        }

        var tr = $($('noscript#earning-clone').html()).clone()
            $('#earnings-table tbody').append(tr)

            tr.find('.rem-earning').click(function() {
                tr.remove()
                compute_total()
            })
            tr.find('[name="earning_amount[]"]').on('change input', function() {
                compute_total()
            })
            tr.find('[name="earning_name[]"]').val('Overtime')

    });
    var salary;

    function compute_total() {
        var earnings = 0,
            deductions = 0,
            net = 0;

        var present = $('#present').val()
        var late_undertime = $('#late_undertime').val()
        var witholding_tax = $('#witholding_tax').val()
        present = present > 0 ? present : 0;
        late_undertime = late_undertime > 0 ? late_undertime : 0;
        witholding_tax = witholding_tax > 0 ? witholding_tax : 0;

        var daily = salary / 6;
        var min = (daily / 8) / 60;
        // ***************CALCULATE EMPLOYEE HOURLY RATE***********************
        var emp_hourly_rate = daily / 8;
        // ***************CALCULATE EMPLOYEE HOURLY RATE***********************
        //alert(present * emp_hourly_rate); 
        $('#earnings-table tbody tr').each(function() {
            var amount = $(this).find('[name="earning_amount[]"]').val()
            earnings += parseFloat(amount > 0 ? amount : 0)
        })
        $('#deductions-table tbody tr').each(function() {
            var amount = $(this).find('[name="deduction_amount[]"]').val()
            deductions += parseFloat(amount > 0 ? amount : 0)
        })
        // net += parseFloat(present) * parseFloat(daily)
        net += parseFloat(present) * parseFloat(emp_hourly_rate)
        net -= parseFloat(late_undertime) * parseFloat(min)
        net += parseFloat(earnings)
        net -= parseFloat(deductions)
        net -= parseFloat((witholding_tax))

        $('#net').val(parseFloat(net).toFixed(3))
    }
    $(function() {
        // **************************************************************************
        $('#payroll_id').change(function() {
            $('#employee_id').prop( "disabled", false );

            $('#date_from').val(this.options[this.selectedIndex].getAttribute('data-from'));
            $('#date_to').val(this.options[this.selectedIndex].getAttribute('data-to'));
        });
        //*******************USED FOR SORTING API TIME ENTRIES DATA*******************
        var sortBy = (function() {
            var toString = Object.prototype.toString,
                // default parser function
                parse = function(x) {
                    return x;
                },
                // gets the item to be sorted
                getItem = function(x) {
                    var isObject = x != null && typeof x === "object";
                    var isProp = isObject && this.prop in x;
                    return this.parser(isProp ? x[this.prop] : x);
                };

            /**
             * Sorts an array of elements.
             *
             * @param {Array} array: the collection to sort
             * @param {Object} cfg: the configuration options
             * @property {String}   cfg.prop: property name (if it is an Array of objects)
             * @property {Boolean}  cfg.desc: determines whether the sort is descending
             * @property {Function} cfg.parser: function to parse the items to expected type
             * @return {Array}
             */
            return function sortby(array, cfg) {
                if (!(array instanceof Array && array.length)) return [];
                if (toString.call(cfg) !== "[object Object]") cfg = {};
                if (typeof cfg.parser !== "function") cfg.parser = parse;
                cfg.desc = !!cfg.desc ? -1 : 1;
                return array.sort(function(a, b) {
                    a = getItem.call(cfg, a);
                    b = getItem.call(cfg, b);
                    return cfg.desc * (a < b ? -1 : +(a > b));
                });
            };

        }());
        //*******************USED FOR SORTING API TIME ENTRIES DATA*******************
        // **************************************************************************

        $('#payroll_id, #employee_id').select2({
            placeholder: 'Please Select Here',
            width: '100%',
        })
        $('#present, #late_undertime, #witholding_tax, #overtime_total').on('change input', function() {
            //alert("test");
            compute_total()
        })
        $('#employee_id').change(function() {

            // ******************************************************************************
            var emp_code = this.options[this.selectedIndex].getAttribute('emp-code');
            var StartDate = $('#date_from').val();
            var EndDate = $('#date_to').val();

            var id = $(this).val()
            var sal = $('#employee_id option[value="' + id + '"]').attr('data-salary')
            //*********************DEFINING SALARY COMPUTATION VARIABLES*********************
            // 8 = "REGULAR NUMBER OF HOURS TO BE RENDERED PER DAY"
            // 125% = "125% WILL BE MULTIPLED TO EMPLOYEE HOURLY RATE TO GET THE OVERTIME HOURLY RATE"
            var emp_rate_per_hour = sal / 8;
            var percentage = 125;
            // Convert percentage to decimal
            var decimal = percentage / 100;
            var overtime_hourly_rate = emp_rate_per_hour * decimal;
            $('#emp_salary').val(sal);
            //alert(result);return;
            //*********************DEFINING SALARY COMPUTATION VARIABLES*********************
            salary = sal > 0 ? parseFloat(sal) : 0;
            $('[name="salary"]').val(salary)
            start_preloader();
            $.ajax({
                type: "GET",
                url: "<?php echo site_url('/Main/get_data_from_sentry') ?>",
                data: {
                    empid: emp_code,
                    StartDate: StartDate,
                    EndDate: EndDate
                },
                //contentType: "application/json; charset=utf-8",
                dataType: "json",

                success: function(data) {
                    //console.log(data);return;
                    var date = new Date(data[1]);
                    //console.log(date);
                    if (data[0].length <= 0) {
                        alert("No Data Found!");
                        return;
                    }
                    //return;
                    $('#present').val(data[0]);

                    $('#overtime_total').val(data[1]);

                    stop_preloader();
                    compute_total()
                },
                error: function (request, status, error) {
                    stop_preloader();
                    alert("User Data Cannot be found in Sentry");
                }
            });

            // *****************************************************************************

        })

        $('#add_earning').click(function() {
            var tr = $($('noscript#earning-clone').html()).clone()
            $('#earnings-table tbody').append(tr)

            tr.find('.rem-earning').click(function() {
                tr.remove()
                compute_total()
            })
            tr.find('[name="earning_amount[]"]').on('change input', function() {
                compute_total()
            })
        })

        $('#add_deduction').click(function() {
            var tr = $($('noscript#deduction-clone').html()).clone()
            $('#deductions-table tbody').append(tr)

            tr.find('.rem-deduction').click(function() {
                tr.remove()
                compute_total()
            })
            tr.find('[name="deduction_amount[]"]').on('change input', function() {
                compute_total()
            })
        })
        $('#save_payslip').click(function() {
            if ($('#net').val() <= 0) {
                alert('Invalid Payslip')
                return false;
            }
            $('#payslip-form').submit()
        })

    })
</script>
<?= $this->endSection() ?>