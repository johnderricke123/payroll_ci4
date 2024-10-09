<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<style>
    hr {
        display: block;
        height: 3px;
        background: transparent;
        width: 100%;
        border: none;
        border-top: solid 1px #aaa;
    }
</style>
<!-- ************************************************************* -->
<p><?php
helper('general');
?></p>
<div class="container py-4">
    <div class="card rounded-0">
        <div class="card-header">
            <div class="d-flex w-100 justify-content-between">
                <div class="col-auto">
                    <div class="card-title h4 mb-0 fw-bolder">Employee's Payslip</div>
                </div>
                <div class="col-auto">
                    <a href="http://localhost/caballes_payroll/Main/payslips" class="btn btn btn-light bg-gradient border rounded-0"><i class="fa fa-angle-left"></i> Back to List</a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="container-fluid" id="printout">
                <?php $i = 0; foreach($payslip_data as $paysl): ?>
                    <hr>
                <div class="row">
                    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                        <div class="lh-1">
                            <dl class="d-flex w-100">
                                <dt class="col-auto">Employee Code:</dt>
                                <dd class="col-auto flex-shrink-1 flex-grow-1 px-2"><?= $paysl['details']['code'] ?></dd>
                            </dl>
                            <dl class="d-flex w-100">
                                <dt class="col-auto">Name:</dt>
                                <dd class="col-auto flex-shrink-1 flex-grow-1 px-2"><?= $paysl['details']['name'] ?> </dd>
                            </dl>
                            <dl class="d-flex w-100">
                                <dt class="col-auto">Department:</dt>
                                <dd class="col-auto flex-shrink-1 flex-grow-1 px-2"><?= $paysl['details']['department'] ?></dd>
                            </dl>
                            <dl class="d-flex w-100">
                                <dt class="col-auto">Designation:</dt>
                                <dd class="col-auto flex-shrink-1 flex-grow-1 px-2"><?= $paysl['details']['designation'] ?></dd>
                            </dl>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                        <div class="lh-1">
                            <dl class="d-flex w-100">
                                <dt class="col-auto">Payroll:</dt>
                                <dd class="col-auto flex-shrink-1 flex-grow-1 px-2"><?= $paysl['details']['payroll_code']." - ".$paysl['details']['payroll_name'] ?></dd>
                            </dl>
                            <dl class="d-flex w-100">
                                <dt class="col-auto">Basic Rate/<sup>week</sup>:</dt>
                                <dd class="col-auto flex-shrink-1 flex-grow-1 px-2"><?= number_format($paysl['details']['salary'], 2) ?></dd>
                            </dl>
                            <dl class="d-flex w-100">
                                <dt class="col-auto">Time Rendered:</dt>
                                <dd class="col-auto flex-shrink-1 flex-grow-1 px-2">
                                <?= isset($paysl['details']['present']) ? (int)$paysl['details']['present'] : 'N/A'; ?>    
                                </dd>
                                <dt class="col-auto">Overtime Rendered:</dt>
                                <dd class="col-auto flex-shrink-1 flex-grow-1 px-2">
                                <?= isset($paysl['details']['overtime']) ?  $paysl['details']['overtime'] : 'N/A'; ?>
                                </dd>
                            </dl>


                        </div>
                    </div>
                </div>
                <hr>
                <div class="row mb-3">
                    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                        <fieldset class="border px-3 py-3 border rounded-0">
                            <legend class="w-auto px-3 mx-3">Earnings</legend>

                            <!-- <div class="container-fluid">
                                <div class="row">
                                    <dl class="d-flex w-100">
                                        <dt class="col-auto">Regular<sup>hours</sup>:</dt>
                                        <dd class="col-auto flex-shrink-1 flex-grow-1 px-2">
                                            <?= (int) $paysl['details']['present'] ?> 
                                        </dd>
                                        <dt class="col-auto">&nbsp;&nbsp;&nbsp;Total:</dt>
                                        <dd class="col-auto flex-shrink-1 flex-grow-1 px-2">
                                            <?= number_format($paysl['details']['total_present'], 2) ?>
                                        </dd>
                                    </dl>
                                    <dl class="d-flex w-100">
                                        <dt class="col-auto">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Total:</dt>
                                        <dd class="col-auto flex-shrink-1 flex-grow-1 px-2">
                                            0.00 </dd>
                                    </dl>
                                </div>
                            </div> -->

                            <table class="table table-striped table-bordered">
                                <colgroup>
                                    <col width="65%">
                                    <col width="35%">
                                </colgroup>
                                <thead>
                                    <tr>
                                        <th class="p-1 text-center">Name</th>
                                        <th class="p-1 text-center">Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $total_earnings = 0;
                                    foreach($paysl['earnings'] as $row):
                                    $total_earnings += $row['amount'];
                                    ?>
                                    <tr>
                                        <td class="px-2 py-1 align-middle"><?= $row['name'] ?></td>
                                        <td class="px-2 py-1 text-end align-middle"><?= number_format($row['amount'],2) ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th class="p-1 text-center">Total Earning</th>
                                        <th class="p-1 text-end"><?= number_format($total_earnings, 2) ?></th>
                                    </tr>
                                </tfoot>
                            </table>


                        </fieldset>
                    </div>

                    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                        <fieldset class="border px-3 py-3 border rounded-0">
                            <legend class="w-auto px-3 mx-3">Deductions</legend>
                            <table class="table table-striped table-bordered">
                                <colgroup>
                                    <col width="65%">
                                    <col width="35%">
                                </colgroup>
                                <thead>
                                    <tr>
                                        <th class="p-1 text-center">Name</th>
                                        <th class="p-1 text-center">Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $total_deductions = 0;
                                    foreach($paysl['deductions'] as $row):
                                    $total_deductions += $row['amount'];
                                    ?>
                                    <tr>
                                        <td class="px-2 py-1 align-middle"><?= $row['name'] ?></td>
                                        <td class="px-2 py-1 text-end align-middle"><?= number_format($row['amount'],2) ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th class="p-1 text-center">Total Deduction</th>
                                        <th class="p-1 text-end"><?= number_format($total_deductions, 2) ?></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </fieldset>
                    </div>
                </div>
                <hr>
                <div class="row mb-3">
                    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                        <dl class="d-flex w-100">
                            <dt class="col-auto">Witholding Tax:</dt>
                            <dd class="col-auto flex-shrink-1 flex-grow-1 px-2">
                                <?= number_format($paysl['details']['witholding_tax'], 2) ?>
                            </dd>
                        </dl>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                        <dl class="d-flex w-100">
                            <dt class="col-auto">Net:</dt>
                            <dd class="col-auto flex-shrink-1 flex-grow-1 px-2"><?= number_format($paysl['details']['net'], 2) ?></dd>
                        </dl>
                    </div>
                </div>

                <?php
                $result = general_check($i);
                if($result == 0){ ?>
                                <p style="page-break-after: always;">&nbsp;</p>
                <?php } 
                ?>

                <hr>
                <?php $i++; endforeach; ?>
            </div>

        </div>
        <div class="card-footer text-center">
            <a href="http://localhost/caballes_payroll/Main/payslip_delete/31" class="btn btn-sm rounded-0 btn-danger bg-gradient" onclick="if(confirm('Are you sure to delete payslip?') !== true) event.preventDefault()"><i class="fa fa-trash"></i> Delete</a>
            <button class="btn btn-sm btn-light rounded-0 border" id="print" type="button"><i class="fa fa-print"></i> Print</button>
        </div>
    </div>
</div>
<!-- *************************************************************** -->
<?= $this->endSection() ?>
<?= $this->section('custom_js') ?>
<script>
    $(function() {
        $('#print').click(function() {
            var h = $('head').clone()
            var el = $('#printout').clone()

            var nw = window.open("", "_blank", "width=" + ($(window).width() * .8) + ",left=" + ($(window).width() * .1) + ",height=" + ($(window).height() * .8) + ",top=" + ($(window).height() * .1))
            nw.document.querySelector('head').innerHTML = h.html()
            nw.document.querySelector('body').innerHTML = el.html()
            nw.document.close()
            setTimeout(() => {
                nw.print()
                setTimeout(() => {
                    nw.close()
                }, 200);
            }, 300);
        })
    })
</script>
<?= $this->endSection() ?>