

<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8" />
		<title>Invoice</title>
        <link rel="stylesheet" type="text/css"  media="screen" href="{{ asset('css/bootstrap.min.css') }}">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" >
		<style>
              @import url('https://fonts.googleapis.com/css2?family=Maven+Pro&display=swap');
              body {
    font-family: 'Maven Pro', sans-serif !important;
    background-color: #374756 !important
}	

@media print {
  .print_button {
    display: none;
  }
}
            .header-elements{
               
                padding-bottom: 12px;
                float: right;
                
            }
			.invoice-box {
				max-width: 700px;
                min-height: 500px;
				margin: auto;
				padding: 30px;
				border: 1px solid #eee;
				font-family: 'Maven Pro', sans-serif !important;
				font-size: 14px;
				line-height: 22px;
				color: black;
                margin-top:70px;
                background-color:#FBFCFC
			}
            .info{
                font-size: 14px;
                color:black !important;
                font-weight:500 !important;
                padding-left:5px;

            }


			.invoice-box table {
				width: 100%;
				line-height: inherit;
				text-align: left;
			}

			.invoice-box table td {
				padding: 5px;
				vertical-align: top;
			}

			.invoice-box table tr td:nth-child(2) {
				text-align: left;
			}

			.invoice-box table tr.top table td {
				padding-bottom: 20px;
			}

			.invoice-box table tr.top table td.title {
				font-size: 45px;
				line-height: 45px;
				color: #333;
			}

			.invoice-box table tr.information table td {
				padding-bottom: 60px;
			}

			.invoice-box table tr.heading td {
				background: #eee;
				border-bottom: 1px solid #ddd;
				font-weight: bold;
                padding: 10px;
               
			}

			.invoice-box table tr.details td {
				
                border-bottom: 1px solid #ddd;
				font-weight: bold;
                padding-bottom: 10px;
			}

			.invoice-box table tr.item td {
				border-bottom: 1px solid #eee;
			}

			.invoice-box table tr.item.last td {
				border-bottom: none;
			}

			.invoice-box table tr.total td:nth-child(2) {
				border-top: 2px solid #eee;
				font-weight: bold;
			}

			@media only screen and (max-width: 600px) {
				.invoice-box table tr.top table td {
					width: 100%;
					display: block;
					text-align: center;
				}

				.invoice-box table tr.information table td {
					width: 100%;
					display: block;
					text-align: center;
				}
			}

			/** RTL **/
			.invoice-box.rtl {
				direction: rtl;
				font-family: Tahoma, 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
			}

			.invoice-box.rtl table {
				text-align: right;
			}

			.invoice-box.rtl table tr td:nth-child(2) {
				text-align: left;
			}
		</style>
	</head>

	<body>
		<div class="invoice-box">
       
         <div class="header-elements"> <a href="{{ route('sim-invoice',['download'=>'pdf','id'=>$invoice->id]) }}"   type="button" class="btn btn-light btn-sm print_button"><i class="fa fa-file mr-2"></i> Download</a> <a type="button" onclick="window.print();return false;" class="btn btn-light btn-sm ml-3 print_button"><i class="fa fa-print mr-2"></i> Print</a> </div>
           
			<table cellpadding="0" cellspacing="0">
				<tr class="top">
					<td colspan="2">
						<table>
							<tr style=" border-top: 2px solid #eee;">
								<td class="title">
									<img src="{{ asset($invoice->logo) }}" style="width: 100%; max-width: 150px" />
								</td>

								<td style="text-align: right">
									<p style="font-weight: bold;font-size:15px">Invoice:<span  style="font-weight: 500">{{ $invoice->invoice_no }}</span></p>
								    <p style="font-weight: bold;font-size:15px">Date: <span style="font-weight: 500">{{ $invoice->date }}</span></p>
								
								</td>
							</tr>
						</table>
					</td>
				</tr>

				<tr class="information">
					<td colspan="2">
						<p style="padding-bottom: 30px">MODULO DI IDENTIFICAZIONE E ATTIVAZIONE DEL SERVIZIO MOBILE PREPAGATO SI DICHIARA A TUTTI GLI
                            EFFETTI DI LEGGE CHE TUTTE LE INFORMAZIONE E I DATI INDICATI NEL PRESENTE DOCUMENTO SONO
                            ACCURATI, COMPLETI VERITIERI</p>
					</td>
				</tr>
                
				<tr class="heading">
					<td>First Name:<span class="info">{{$invoice->first}}</span></td>

					<td>Last Name:<span class="info">{{$invoice->last}}</span></td>
				</tr>

				<tr class="details">
					<td>Gender:<span class="info">{{$invoice->gender}}</span></td>

					<td>Date of Birth: <span class="info">{{$invoice->dob}}</span></td>
				</tr>

                <tr class="heading">
					<td>Sim Number:<span class="info">{{$invoice->sim_number}}</span></td>

					<td>ICCID Number: <span class="info">{{$invoice->iccid}}</span></td>
				</tr>

                <tr class="details">
					<td>Codice::<span class="info">{{$invoice->codice}}</span></td>

					<td>Nazionalità: <span class="info">{{$invoice->nationality}}</span></td>
				</tr>
				@php
				$alt = DB::table('sim_orders')->where('iccid', $invoice->iccid)->latest()->first();

				$iccid = $alt->alt_iccid;
				$sim_number = $alt->alt_sim_number;
				$operator = $alt->alt_operator;
				$ricarica = $alt->recharge;
				$sim_number = $alt->sim_number; 
				@endphp

				@if ($iccid != null)
				<tr class="heading">
					<td> Protabilita Sim Number : <span class="info">{{$sim_number}}</span> </td>
					<td>  Portabilita ICCID Number : <span class="info">{{$iccid}}</span> </td>
				</tr>
				<tr class="details">
					<td> Portabilita Operator : <span class="info"> {{$operator}}</span> </td>
				</tr>
				@endif

				<tr class="total">
					<td></td>

					<td  style="text-align: right;padding-right:36px;padding-top:20px;font-size:22px">Total: <span>{{$invoice->price}}</span></td>
				</tr>
			</table>
		</div>
	</body>
	
</html>