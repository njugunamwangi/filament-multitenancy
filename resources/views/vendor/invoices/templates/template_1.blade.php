<!DOCTYPE html>
<html lang="zxx">
<head>
    <title>{{ $invoice->name }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="UTF-8">

    <!-- External CSS libraries -->
    <link type="text/css" rel="stylesheet" href="css/bootstrap.min.css">
    <link type="text/css" rel="stylesheet" href="fonts/font-awesome/css/font-awesome.min.css">

    <!-- Google fonts -->
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Poppins:100,200,300,400,500,600,700,800,900">

    <!-- Custom Stylesheet -->
    <link type="text/css" rel="stylesheet" href="css/style.css">
</head>
<body class="w-full">

<!-- Invoice 1 start -->
<div class="invoice-1 invoice-content">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="invoice-inner clearfix">
                    <div class="invoice-info clearfix" id="invoice_wrapper">
                        <div class="invoice-headar">
                            <div class="row g-0">
                                <div class="col-sm-6">
                                    @if ($invoice->logo)
                                        <div class="invoice-logo">
                                            <!-- logo started -->
                                            <div class="logo">
                                                <img src="{{ $invoice->getLogo() }}" alt="logo">
                                            </div>
                                            <!-- logo ended -->
                                        </div>
                                    @endif

                                </div>
                                <div class="col-sm-6 invoice-id">
                                    <div class="info">
                                        <h1 class="color-white inv-header-1">{{ $invoice->name }}</h1>
                                        <p class="color-white mb-1">{{ $invoice->name }} Number <span>{{ $invoice->getSerialNumber() }}</span></p>
                                        <p class="color-white mb-0">{{ $invoice->name }} Date <span>{{ $invoice->getDate() }}</span></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="invoice-top">
                            <div class="row flex-container">
                                <div class="col-sm-6">
                                    <div class="invoice-number mb-30">
                                        <h4 class="inv-title-1">{{ $invoice->name }} To</h4>
                                        @if ($invoice->buyer->name)
                                            <h2 class="name mb-8">{{ $invoice->buyer->name }}</h2>
                                        @endif
                                        <p class="invo-addr-1">
                                            @if ($invoice->buyer->phone)

                                            {{ __('invoices::invoice.phone') }}: {{ $invoice->buyer->phone }} <br/>
                                            @endif

                                            @if ($invoice->buyer->email)
                                                {{ $invoice->buyer->email }}<br/>
                                            @endif

                                            @if ($invoice->buyer->address)
                                                {{ $invoice->buyer->address }} <br/>
                                            @endif
                                        </p>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="invoice-number mb-30">
                                        <div class="invoice-number-inner">
                                            <h4 class="inv-title-1">{{ $invoice->name }} From</h4>
                                            @if ($invoice->seller->name)
                                                <h2 class="name mb-10">{{ $invoice->seller->name }}</h2>
                                            @endif
                                            <p class="invo-addr-1">
                                                @if ($invoice->seller->email)
                                                    {{ $invoice->seller->email }} <br/>
                                                @endif

                                                @if ($invoice->seller->address)
                                                    {{ $invoice->seller->address }} <br/>
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="invoice-center">
                            <div class="table-responsive">
                                <table class="table mb-0 table-striped invoice-table">
                                    <thead class="bg-active">
                                    <tr class="tr">
                                        <th>No.</th>
                                        <th class="pl0 text-start">Item Description</th>
                                        <th class="text-center">Price</th>
                                        <th class="text-center">Quantity</th>
                                        <th class="text-end">Amount</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr class="tr">
                                        <td>
                                            <div class="item-desc-1">
                                                <span>01</span>
                                            </div>
                                        </td>
                                        <td class="pl0">Businesscard Design</td>
                                        <td class="text-center">$300</td>
                                        <td class="text-center">2</td>
                                        <td class="text-end">$600.00</td>
                                    </tr>
                                    <tr class="bg-grea">
                                        <td>
                                            <div class="item-desc-1">
                                                <span>02</span>

                                            </div>
                                        </td>
                                        <td class="pl0">Fruit Flayer Design</td>
                                        <td class="text-center">$400</td>
                                        <td class="text-center">1</td>
                                        <td class="text-end">$60.00</td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="item-desc-1">
                                                <span>03</span>
                                            </div>
                                        </td>
                                        <td class="pl0">Application Interface Design</td>
                                        <td class="text-center">$240</td>
                                        <td class="text-center">3</td>
                                        <td class="text-end">$640.00</td>
                                    </tr>

                                    <tr>
                                        <td>
                                            <div class="item-desc-1">
                                                <span>04</span>
                                            </div>
                                        </td>
                                        <td class="pl0">Theme Development</td>
                                        <td class="text-center">$720</td>
                                        <td class="text-center">4</td>
                                        <td class="text-end">$640.00</td>
                                    </tr>
                                    <tr class="tr2">
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td class="text-center">SubTotal</td>
                                        <td class="text-end">$710.99</td>
                                    </tr>
                                    <tr class="tr2">
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td class="text-center">Tax</td>
                                        <td class="text-end">$85.99</td>
                                    </tr>
                                    <tr class="tr2">
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td class="text-center f-w-600 active-color">Grand Total</td>
                                        <td class="f-w-600 text-end active-color">$795.99</td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="invoice-bottom">
                            <div class="row">
                                <div class="col-lg-6 col-md-8 col-sm-7">
                                    <div class="mb-30 dear-client">
                                        <h3 class="inv-title-1">Terms & Conditions</h3>
                                        <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been typesetting industry. Lorem Ipsum</p>
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-4 col-sm-5">
                                    <div class="mb-30 payment-method">
                                        <h3 class="inv-title-1">Payment Method</h3>
                                        <ul class="payment-method-list-1 text-14">
                                            <li><strong>Account No:</strong> 00 123 647 840</li>
                                            <li><strong>Account Name:</strong> Jhon Doe</li>
                                            <li><strong>Branch Name:</strong> xyz</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="invoice-contact clearfix">
                            <div class="row g-0">
                                <div class="col-lg-9 col-md-11 col-sm-12">
                                    <div class="contact-info">
                                        <a href="tel:+55-4XX-634-7071"><i class="fa fa-phone"></i> +00 123 647 840</a>
                                        <a href="tel:info@themevessel.com"><i class="fa fa-envelope"></i> info@themevessel.com</a>
                                        <a href="tel:info@themevessel.com" class="mr-0 d-none-580"><i class="fa fa-map-marker"></i> 169 Teroghoria, Bangladesh</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Invoice 1 end -->

<script src="js/jquery.min.js"></script>
<script src="js/jspdf.min.js"></script>
<script src="js/html2canvas.js"></script>
<script src="js/app.js"></script>
</body>
</html>
