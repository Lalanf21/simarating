<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8" />
	</head>

	<body>
		<div style="
        max-width: 800px;
        margin: auto;
        padding: 30px;
        border: 1px solid #eee;
        font-size: 16px;
        line-height: 24px;
        font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
        color: #555;">

            <div style="margin-bottom:10px;">To,</div>
            <div style="margin-bottom: 20px;">
                <p>{{ $nama }}</p>
            </div>

            <div>
                <p>Your detail reservation</p>
            </div>

			<table cellpadding="0" cellspacing="0" style="width: 100%;
            line-height: inherit;
            text-align: left;">

				<tr style="background: #eee;
				border-bottom: 1px solid #ddd;
				font-weight: bold;">
					<td>Transaction number</td>
				</tr>
				<tr>
					<td style="padding-bottom: 20px;" >
                        {{ $no_transaksi }}
                    </td>
				</tr>

				<tr style="background: #eee;
				border-bottom: 1px solid #ddd;
				font-weight: bold;">
					<td>Company name</td>
				</tr>
				<tr class="details">
					<td style="padding-bottom: 20px;" >
                        {{ $nama_perusahaan }}
                    </td>
				</tr>

				<tr style="background: #eee;
				border-bottom: 1px solid #ddd;
				font-weight: bold;">
					<td>Reservation date</td>
				</tr>
				<tr class="details">
					<td style="padding-bottom: 20px;" >
                        {{ date_en_full($booking_date) }}
                    </td>
				</tr>

                <tr style="font-weight: bold;">
					<td>Qr code</td>
				</tr>
                <tr>
                    <td>
                        <img src="{{ asset('/storage/img/qr-codes/'. $qrCode .'/qrcode.png') }}">
                    </td>
                </tr>

				
			</table>
            <div class="footer" style="font-size:12px;">
                <div style="margin-bottom:10px;margin-top:50px;">Best Regards,</div>
                <div></div><b>PRADITA PARTNER LOUNGE @PRADITA UNIVERSITY</b></div>
                <div>Phone : 021-5568 9999 (214)</div>
                <div>Scientia Business Park Tower 1</div>
                <div>Jl. Boulevard Gading Serpong Blok O/1</div>
            </div>
		</div>

	</body>
</html>