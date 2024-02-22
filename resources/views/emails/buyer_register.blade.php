<!DOCTYPE html>
<html lang="en">
	<head>
		<!-- Required meta tags -->
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
		<title>Profitley</title>
		<style type="text/css">
			body{font-family: arial}
		</style>
	</head>
	<body>
		<table cellpadding="0" cellspacing="0" border="0" width="650" align="center">
			<tbody>
				<tr>
					<td style="padding-bottom: 15px; padding-top: 10px;"><img src="https://profitley.com/email-templates/images/softude.png" alt="profitley"></td>
				</tr>						
			</tbody>
		</table>
		<!-- Banner content start -->
		<table cellpadding="0" cellspacing="0" border="0" width="650" align="center">
			<tbody>
				<tr>
					<td style="background: #0048BA; width: 1%; padding-right: 15px; padding-left: 15px; padding-top: 10px; padding-bottom: 10px"><img src="https://profitley.com/email-templates/images/cart-icon.png" alt="profitley"></td>
					<td style="background: #0048BA; padding-left: 15px;padding-top: 10px; padding-bottom: 10px">
						<table>
							<tr>
								<td style="font-family: arial; font-size: 32px; font-weight: 700; color: #fff;">New Buyer Registered</td>
							</tr>
							<tr>
								<td style="font-family: arial; font-size:16px; font-weight: normal; color: #fff;">{{ $body->shop_name }} </td>
							</tr>
							<tr>
								<td style="font-family: arial; font-size: 16px; font-weight: normal; color: #fff;">{{ $body->name.' '.$body->last_name }}</td>
							</tr>
						</table>
					</td>
				</tr>						
			</tbody>
		</table>
		<!-- Content Area Start -->
		<table cellpadding="0" cellspacing="0" border="0" width="650" align="center">
			<tbody>
				<tr>
					<td style="color:#FF8000; font-size: 14px; font-family: arial; padding-top: 20px; padding-bottom: 10px; font-weight: 600">Hello {{ $name }}</td>
				</tr>
				<tr>
					<td style="color:#000; font-size: 14px; font-family: arial; padding-top: 10px; padding-bottom: 10px; font-weight: normal">New buyer has sign up. Awaiting your approval. Below are the details.</td>
				</tr>						
			</tbody>
		</table>
		<table cellpadding="0" cellspacing="0" border="0" width="650" align="center">
			<tbody>
				<tr>
					<td width="30%" style="color:000; font-size: 14px; font-family: arial; padding-top:5px; padding-bottom:5px; font-weight: 600">Name:</td>
					<td width="70%" style="color:000; font-size: 14px; font-family: arial; padding-top:5px; padding-bottom:5px; font-weight: 600">{{ $body->name.' '.$body->last_name }}</td>
				</tr>
				<tr>
					<td width="30%" style="color:000; font-size: 14px; font-family: arial; padding-top:5px; padding-bottom:5px; font-weight: 600">Shop Name:</td>
					<td width="70%" style="color:000; font-size: 14px; font-family: arial; padding-top:5px; padding-bottom:5px; font-weight: 600">{{ $body->shop_name }}</td>
				</tr>
				<tr>
					<td width="30%" style="color:000; font-size: 14px; font-family: arial; padding-top:5px; padding-bottom:5px; font-weight: 600">Phone:</td>
					<td width="70%" style="color:000; font-size: 14px; font-family: arial; padding-top:5px; padding-bottom:5px; font-weight: normal">{{ $body->phone_number }}</td>
				</tr>
				<tr>
					<td width="30%" style="color:000; font-size: 14px; font-family: arial; padding-top:5px; padding-bottom:5px; font-weight: 600">GST:</td>
					<td width="70%" style="color:000; font-size: 14px; font-family: arial; padding-top:5px; padding-bottom:5px; font-weight: normal">{{ $body->gst }}</td>
				</tr>
				<tr>
					<td width="30%" style="color:000; font-size: 14px; font-family: arial; padding-top:5px; padding-bottom:5px; font-weight: 600">Address:</td>
					<td width="70%" style="color:000; font-size: 14px; font-family: arial; padding-top:5px; padding-bottom:5px; font-weight: normal">{{ $body->address1 }}<br>{{ $body->address2 }}<br>{{ $body->cityName }}-{{ $body->pincode }}<br>{{ $body->stateName }}</td>
				</tr>
				<tr>
					<td width="30%" style="color:000; font-size: 14px; font-family: arial; padding-top:5px; padding-bottom:5px; font-weight: 600">City:</td>
					<td width="70%" style="color:000; font-size: 14px; font-family: arial; padding-top:5px; padding-bottom:5px; font-weight: normal">{{ $body->cityName }}</td>
				</tr>
				<tr>
					<td width="30%" style="color:000; font-size: 14px; font-family: arial; padding-top:5px; padding-bottom:5px; font-weight: 600">State:</td>
					<td width="70%" style="color:000; font-size: 14px; font-family: arial; padding-top:5px; padding-bottom:5px; font-weight: normal">{{ $body->stateName }}</td>
				</tr>	
				<tr>
					<td colspan="2" style="height:20px;">&nbsp;</td>
				</tr>						
			</tbody>
		</table>
		<!-- content Area End -->
		<!-- footer start -->
		<table cellpadding="0" cellspacing="0" border="0" width="650" align="center">
			<tbody>
				<tr>
					<td style="background: #0048BA; padding-left: 15px; padding-top: 10px; padding-bottom: 10px"><img src="https://profitley.com/email-templates/images/logo-light.png" alt="profitley"></td>
					<td style="background:#0048BA; text-align: right; padding-right: 15px; padding-top: 10px; padding-bottom: 10px">
						<a href="#" target="_blank" style="color: #fff; font-size: 14px; text-decoration: none; font-family: arial">Help</a>
					</td>
				</tr>
			</tbody>
		</table>
		<table cellpadding="0" cellspacing="0" border="0" width="650" align="center" style="border-bottom: 1px solid #D6D4D4">
			<tbody>
				<tr>
					<td style="padding-left:0px; padding-top: 10px; font-size: 12px; padding-bottom: 10px; font-family: arial;">©2021 Profitley. All Rights Reserved.</td>
					<td style="text-align: right; padding-right:0px; padding-top: 10px; padding-bottom: 10px; font-family: arial;">
						
						<a href="#" target="_blank" style="color: #000; font-size: 12px; text-decoration: none; font-family: arial">Facebook</a> | 
						<a href="#" target="_blank" style="color: #000; font-size: 12px; text-decoration: none; font-family: arial">Twitter</a> | 
						<a href="#" target="_blank" style="color: #000; font-size: 12px; text-decoration: none; font-family: arial">LinkedIn</a>
					</td>
				</tr>
			</tbody>
		</table>
		<table cellpadding="0" cellspacing="0" border="0" width="650" align="center">
			<tbody>
				<tr>
					<td style="padding-left:0px; padding-top: 10px; font-size: 11px; padding-bottom: 10px; color: #7F7F7F; font-family: arial;">This email was sent from a notification-only address that cannot accept incoming email. Please do not reply to this message.</td>
				</tr>
			</tbody>
		</table>
	</body>
</html>