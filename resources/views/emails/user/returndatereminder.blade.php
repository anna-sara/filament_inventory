<!DOCTYPE html>
<html>
<head>
    <title>Påminnelse om återlämning</title>
</head>
<body style="font-family: 'Poppins', Arial, sans-serif">
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
            <td align="center" style="padding: 20px;">
                <table class="content" width="600" border="0" cellspacing="0" cellpadding="0" style="border-collapse: collapse; border: 1px solid #cccccc;">
                    <!-- Header -->
                    <tr>
                        <td class="header" style="background-color: #0080bb; padding: 20px; text-align: center; color: white; font-size: 18px;">
                            <img style="height: 80px; width: auto"class=" w-auto h-full" src="https://boka.vbytes.se/img/logo.png" alt="logo">
                            
                        </td>
                    </tr>

                    <!-- Body -->
                    <tr>
                        <td class="body" style="padding: 40px; text-align: left; font-size: 16px; line-height: 1.6;">
                            <h1  style="margin: 0;">Påminnelse om återlämning</h1>
                            <p>Det är snart dags att lämna tillbaka {{ $reservationDesc }}</p>
                            <p>Du ska återlämna varan {{ $reservationReturnDate }}</p>
                            <p>Med vänliga hälsningar</p>
                            <p>vBytes</p>
                        </td>
                    </tr>
                    <!-- Footer -->
                    <tr>
                        <td class="footer" style="background-color: #0080bb; padding: 40px; text-align: center; color: white; font-size: 14px;">
                        Since 2020 | vBytes
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>