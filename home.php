<?
    /*
        The PHP Mortgage Calculator tries to figure out a home
        owners mortgage payments, and the breakdown of each monthly
        payment.

        The calculator accepts:
            Price (cost of home in US Dollars)
            Percentage of Down Payment
            Length of Mortgage
            Annual Interest Rate

        Based on the four items that the user enters, we can figure
        out the down payment (in US Dollars), the ammount that the
        buyer needs to finance, and the monthly finance payment.
        The calculator can also break down the monthly payments
        so we know how much goes towards the mortgage's interest,
        the mortgage's principal, the loan's Private Mortgage Insurance
        (if less that 20% was used as a down payment), and an rough
        estimate of the property's residential tax

        The calculations are visualized with the aid of two
        diagrams at the bottom of the page, the first one
        displaying the remaining balance, the second showing
        the interplay of monthly paid interest vs. monthly paid
        principal, both on a month/money coordinate system.

        [ See below for LICENSE ]
    */


    /* --------------------------------------------------- *
     * Set Form DEFAULT values
     * --------------------------------------------------- */
    $default_sale_price              = "$200,000";
    $default_annual_interest_percent = 6.5;
    $default_year_term               = 30;
    $default_down_percent            = 10;
    $default_show_progress           = TRUE;
    /* --------------------------------------------------- */

    /* --------------------------------------------------- *
     * Set the USER INPUT values
     * --------------------------------------------------- */
    $sale_price                      = $HTTP_GET_VARS['sale_price'];
    $annual_interest_percent         = $HTTP_GET_VARS['annual_interest_percent'];
    $year_term                       = $HTTP_GET_VARS['year_term'];
    $down_percent                    = $HTTP_GET_VARS['down_percent'];
    $show_progress                   = $HTTP_GET_VARS['show_progress'];
    /* --------------------------------------------------- */

    // If HTML headers have not already been sent, we'll print some here
    if (!headers_sent()) {
        print("<html>\n");
        print("<head><title>Mortgage Calculator</title></head>\n");
        print("<body bgcolor=\"#ffffff\">\n");
        print("<h1>PHP Mortgage Calculator</h1>\n");
        print("Copyright (c) 2002 by  <a href=\"http://dave.imarc.net/mortgage\" target=\"_blank\">David Tufts</a><br>\n");
        print("Copyright (c) 2002 <a href=\"#graphics\">Graphics</a> by  <a href=\"http://www.karakas-online.de\" target=\"_top\">Chris Karakas</a><br>\n");
        print("<hr>\n\n");
        $print_footer = TRUE;
    } else {
        $print_footer = FALSE;
    }




    // Style Sheet
    ?>
    <style type="text/css">
        <!--
            td {
                font-size : 11px;
                font-family : verdana, helvetica, arial, lucidia, sans-serif;
                color : #000000;
            }
        -->
    </style>
    <?
    /* --------------------------------------------------- */
    // This function does the actual mortgage calculations
    // by plotting a PVIFA (Present Value Interest Factor of Annuity)
    // table...
    function get_interest_factor($year_term, $monthly_interest_rate) {
        global $base_rate;

        $factor      = 0;
        $base_rate   = 1 + $monthly_interest_rate;
        $denominator = $base_rate;
        for ($i=0; $i < ($year_term * 12); $i++) {
            $factor += (1 / $denominator);
            $denominator *= $base_rate;
        }
        return $factor;
    }
    /* --------------------------------------------------- */
    if (!$sale_price && !$form_complete)              { $sale_price              = $default_sale_price;              }
    if (!$annual_interest_percent && !$form_complete) { $annual_interest_percent = $default_annual_interest_percent; }
    if (!$year_term && !$form_complete)               { $year_term               = $default_year_term;               }
    if (!$down_percent && !$form_complete)            { $down_percent            = $default_down_percent;            }
    if (!$show_progress && !$form_complete)           { $show_progress           = $default_show_progress;           }
    // If the form is complete, we'll start the math
    if ($form_complete) {
        // We'll set all the numerec values to JUST
        // numbers - this will delete any dollars signs,
        // commas, spaces, and letters, without invalidating
        // the value of the number
        $sale_price              = ereg_replace( "[^0-9.]", "", $sale_price);
        $annual_interest_percent = eregi_replace("[^0-9.]", "", $annual_interest_percent);
        $year_term               = eregi_replace("[^0-9.]", "", $year_term);
        $down_percent            = eregi_replace("[^0-9.]", "", $down_percent);
        $month_term              = $year_term * 12;
        $down_payment            = $sale_price * ($down_percent / 100);
        $annual_interest_rate    = $annual_interest_percent / 100;
        $monthly_interest_rate   = $annual_interest_rate / 12;
        $financing_price         = $sale_price - $down_payment;
        $monthly_factor          = get_interest_factor($year_term, $monthly_interest_rate);
        $monthly_payment         = $financing_price / $monthly_factor;
    }
?>
<form method="GET" name="information" action="<?= $HTTP_SERVER_VARS['PHP_SELF']; ?>">
<input type="hidden" name="form_complete" value="1">
<table cellpadding="2" cellspacing="0" border="0" width="100%">
    <tr valign="top">
        <td align="right"><img src="/images/clear.gif" width="225" height="1" border="0" alt=""></td>
        <td align="smalltext" width="100%"><img src="/images/clear.gif" width="250" height="1" border="0" alt=""></td>
    </tr>
    <tr valign="top" bgcolor="#cccccc">
        <td align="center" colspan="2"><b>Purchase &amp; Financing Information</b></td>
    </tr>
    <tr valign="top" bgcolor="#eeeeee">
        <td align="right">Sale Price of Home:</td>
        <td width="100%"><input type="text" size="10" name="sale_price" value="<?= $sale_price; ?>">Currency is irrelevant</td>
    </tr>
    <tr valign="top" bgcolor="#eeeeee">
        <td align="right">Percentage Down:</td>
        <td><input type="text" size="5" name="down_percent" value="<?= $down_percent; ?>">%</td>
    </tr>
    <tr valign="top" bgcolor="#eeeeee">
        <td align="right">Length of Mortgage:</td>
        <td><input type="text" size="3" name="year_term" value="<?= $year_term; ?>">years</td>
    </tr>
    <tr valign="top" bgcolor="#eeeeee">
        <td align="right">Annual Interest Rate:</td>
        <td><input type="text" size="5" name="annual_interest_percent" value="<?= $annual_interest_percent; ?>">%</td>
    </tr>
    <tr valign="top" bgcolor="#eeeeee">
        <td align="right">Explain Calculations:</td>
        <td><input type="checkbox" name="show_progress" value="1" <? if ($show_progress) { print("checked"); } ?>> Show me the calculations and amortization</td>
    </tr>
    <tr valign="top" bgcolor="#eeeeee">
        <td>&nbsp;</td>
        <td><input type="submit" value="Calculate"><br><? if ($form_complete) { print("<a href=\"" . $HTTP_SERVER_VARS['PHP_SELF'] . "\">Start Over</a><br>"); } ?><br></td>
    </tr>
<?
    // If the form has already been calculated, the $down_payment
    // and $monthly_payment variables will be figured out, so we
    // can show them in this table

    if ($monthly_payment) {
?>
        <tr valign="top">
            <td align="center" colspan="2" bgcolor="#000000"><font color="#ffffff"><b>Mortgage Payment Information</b></font></td>
        </tr>
        <tr valign="top" bgcolor="#eeeeee">
            <td align="right">Down Payment:</td>
            <td><b><?= "\$" . number_format($down_payment, "2", ".", "thousands_sep"); ?></b></td>
        </tr>
        <tr valign="top" bgcolor="#eeeeee">
            <td align="right">Amount Financed:</td>
            <td><b><?= "\$" . number_format($financing_price, "2", ".", "thousands_sep"); ?></b></td>
        </tr>
        <tr valign="top" bgcolor="#cccccc">
            <td align="right">Monthly Payment:</td>
            <td><b><?= "\$" . number_format($monthly_payment, "2", ".", "thousands_sep"); ?></b><br><font>(Principal &amp; Interest ONLY)</font></td>
        </tr>
        <?
            if ($down_percent < 20) {
                $pmi_per_month = 55 * ($financing_price / 100000);
        ?>
                <tr valign="top" bgcolor="#FFFFCC">
                    <td align="right">&nbsp;</td>
                    <td>
                        <br>
                        Since you are putting LESS than 20% down, you will need to pay PMI (<a href="http://www.google.com/search?hl=en&q=private+mortgage+insurance">Private Mortgage Insurance</a>), which tends to be about $55 per month for every $100,000 financed (until you have paid off 20% of your loan). This could add <?= "\$" . number_format($pmi_per_month, "2", ".", "thousands_sep"); ?> to your monthly payment.
                    </td>
                </tr>
                <tr valign="top" bgcolor="#FFFF99">
                    <td align="right">Monthly Payment:</td>
                    <td><b><?= "\$" . number_format(($monthly_payment + $pmi_per_month), "2", ".", "thousands_sep"); ?></b><br><font>(Principal &amp; Interest, and PMI)</td>
                </tr>
        <?
            }
        ?>
        <tr valign="top" bgcolor="#CCCCFF">
            <td align="right">&nbsp;</td>
            <td>
                <br>
                <?
                    $assessed_price          = ($sale_price * .85);
                    $residential_yearly_tax  = ($assessed_price / 1000) * 14;
                    $residential_monthly_tax = $residential_yearly_tax / 12;

                    if ($pmi_per_month) {
                        $pmi_text = "PMI and ";
                    }
                ?>
                Residential (or Property) Taxes are a little harder to figure out... In Massachusetts, the average resedential tax rate seems to be around $14 per year for every $1,000 of your property's assessed value.
                <br><br>
                Let's say that your property's <i>assessed value</i> is 85% of what you actually paid for it - <?= "\$" . number_format($assessed_price, "2", ".", "thousands_sep"); ?>. This would mean that your yearly residential taxes will be around <?= "\$" . number_format($residential_yearly_tax, "2", ".", "thousands_sep"); ?>
                This could add <?= "\$" . number_format($residential_monthly_tax, "2", ".", "thousands_sep"); ?> to your monthly payment.
            </td>
        </tr>
        <tr valign="top" bgcolor="#9999FF">
            <td align="right">TOTAL Monthly Payment:</td>
            <td><b><?= "\$" . number_format(($monthly_payment + $pmi_per_month + $residential_monthly_tax), "2", ".", "thousands_sep"); ?></b><br><font>(including <?= $pmi_text; ?> residential tax)</font></td>
        </tr>
<?
    }
?>
</table>
</form>
<?
    // This prints the calculation progress and
    // the instructions of HOW everything is figured
    // out
    if ($form_complete && $show_progress) {
        $step = 1;
?>
        <br><br>
        <table cellpadding="5" cellspacing="0" border="1" width="100%">
            <tr valign="top">
                <td><b><?= $step++; ?></b></td>
                <td>
                    The <b>down payment</b> = The price of the home multiplied by the percentage down divided by 100 (for 5% down becomes 5/100 or 0.05)<br><br>
                    $<?= number_format($down_payment,"2",".","thousands_sep"); ?> = $<?= number_format($sale_price,"2",".","thousands_sep"); ?> X (<?= $down_percent; ?> / 100)
                </td>
            </tr>
            <tr valign="top">
                <td><b><?= $step++; ?></b></td>
                <td>
                    The <b>interest rate</b> = The annual interest percentage divided by 100<br><br>
                    <?= $annual_interest_rate; ?> = <?= $annual_interest_percent; ?>% / 100
                </td>
            </tr>
            <tr valign="top" bgcolor="#cccccc">
                <td colspan="2">
                    The <b>monthly factor</b> = The result of the following formula:
                </td>
            </tr>
            <tr valign="top">
                <td><b><?= $step++; ?></b></td>
                <td>
                    The <b>monthly interest rate</b> = The annual interest rate divided by 12 (for the 12 months in a year)<br><br>
                    <?= $monthly_interest_rate; ?> = <?= $annual_interest_rate; ?> / 12
                </td>
            </tr>
            <tr valign="top">
                <td><b><?= $step++; ?></b></td>
                <td>
                    The <b>month term</b> of the loan in months = The number of years you've taken the loan out for times 12<br><br>
                    <?= $month_term; ?> Months = <?= $year_term; ?> Years X 12
                </td>
            </tr>
            <tr valign="top">
                <td><b><?= $step++; ?></b></td>
                <td>
                    The montly payment is figured out using the following formula:<br>
                    Monthly Payment = <?= number_format($financing_price, "2", "", ""); ?> * (<?= number_format($monthly_interest_rate, "4", "", ""); ?> / (1 - ((1 + <?= number_format($monthly_interest_rate, "4", "", ""); ?>)<sup>-(<?= $month_term; ?>)</sup>)))
                    <br><br>
                    The <a href="#amortization">amortization</a> breaks down how much of your monthly payment goes towards the bank's interest, and how much goes into paying off the principal of your loan.
                </td>
            </tr>
        </table>
        <br>
<?
        // Set some base variables
        $principal     = $financing_price;
        $current_month = 1;
        $current_year  = 1;
        // This basically, re-figures out the monthly payment, again.
        $power = -($month_term);
        $denom = pow((1 + $monthly_interest_rate), $power);
        $monthly_payment = $principal * ($monthly_interest_rate / (1 - $denom));

        print("<br><br><a name=\"amortization\"></a>Amortization For Monthly Payment: <b>\$" . number_format($monthly_payment, "2", ".", "thousands_sep") . "</b> over " . $year_term . " years<br>\n");
        print("<table cellpadding=\"5\" cellspacing=\"0\" bgcolor=\"#eeeeee\" border=\"1\" width=\"100%\">\n");

        // This LEGEND will get reprinted every 12 months
        $legend  = "\t<tr valign=\"top\" bgcolor=\"#cccccc\">\n";
        $legend .= "\t\t<td align=\"right\"><b>Month</b></td>\n";
        $legend .= "\t\t<td align=\"right\"><b>Interest Paid</b></td>\n";
        $legend .= "\t\t<td align=\"right\"><b>Principal Paid</b></td>\n";
        $legend .= "\t\t<td align=\"right\"><b>Remaing Balance</b></td>\n";
        $legend .= "\t</tr>\n";

        echo $legend;

        // Loop through and get the current month's payments for
        // the length of the loan
        while ($current_month <= $month_term) {
            $interest_paid     = $principal * $monthly_interest_rate;
            $principal_paid    = $monthly_payment - $interest_paid;
            $remaining_balance = $principal - $principal_paid;

            // Compute the data arrays for interest, principal and balance
            $interest_array[$current_month] = array( "", $current_month, $interest_paid);
            $principal_array[$current_month] = array( "", $current_month, $principal_paid);
            $balance_array[$current_month] = array( "", $current_month, $remaining_balance);

            $this_year_interest_paid  = $this_year_interest_paid + $interest_paid;
            $this_year_principal_paid = $this_year_principal_paid + $principal_paid;

            print("\t<tr valign=\"top\" bgcolor=\"#eeeeee\">\n");
            print("\t\t<td align=\"right\">" . $current_month . "</td>\n");
            print("\t\t<td align=\"right\">\$" . number_format($interest_paid, "2", ".", "thousands_sep") . "</td>\n");
            print("\t\t<td align=\"right\">\$" . number_format($principal_paid, "2", ".", "thousands_sep") . "</td>\n");
            print("\t\t<td align=\"right\">\$" . number_format($remaining_balance, "2", ".", "thousands_sep") . "</td>\n");
            print("\t</tr>\n");

            ($current_month % 12) ? $show_legend = FALSE : $show_legend = TRUE;

            if ($show_legend) {
                print("\t<tr valign=\"top\" bgcolor=\"#ffffcc\">\n");
                print("\t\t<td colspan=\"4\"><b>Totals for year " . $current_year . "</td>\n");
                print("\t</tr>\n");

                $total_spent_this_year = $this_year_interest_paid + $this_year_principal_paid;
                print("\t<tr valign=\"top\" bgcolor=\"#ffffcc\">\n");
                print("\t\t<td>&nbsp;</td>\n");
                print("\t\t<td colspan=\"3\">\n");
                print("\t\t\tYou will spend \$" . number_format($total_spent_this_year, "2", ".", "thousands_sep") . " on your house in year " . $current_year . "<br>\n");
                print("\t\t\t\$" . number_format($this_year_interest_paid, "2", ".", "thousands_sep") . " will go towards INTEREST<br>\n");
                print("\t\t\t\$" . number_format($this_year_principal_paid, "2", ".", "thousands_sep") . " will go towards PRINCIPAL<br>\n");
                print("\t\t</td>\n");
                print("\t</tr>\n");

                print("\t<tr valign=\"top\" bgcolor=\"#ffffff\">\n");
                print("\t\t<td colspan=\"4\">&nbsp;<br><br></td>\n");
                print("\t</tr>\n");

                $current_year++;
                $this_year_interest_paid  = 0;
                $this_year_principal_paid = 0;

                if (($current_month + 6) < $month_term) {
                    echo $legend;
                }
            }

            $principal = $remaining_balance;
            $current_month++;
        }
        print("</table>\n");
    }


//*******************************  Graphics ******************************//
// This part Copyright (c) 2002 Chris Karakas <chris@karakas-online.de>
// http://www.karakas-online.de
//
// Compute the graph here
include ( "./phplot.php");

if (isset($balance_array["1"])){

  $graph = new PHPlot;

  $tmpdir = "./tmp";
  $prefix = "chart";
  $maxbytes = 10000000;
  //Create a file with random name in the temp dir, starting with $prefix
  $temp = tempnam($tmpdir,$prefix);
  //Add the .png ending and rename it, otherwise some web servers will not serve it.
  $temp_png = $temp . '.' . "png";
  rename($temp,$temp_png);
  //Specify this random file to be the output file of the image
  $graph->SetOutputfile($temp_png);
  $graph->SetFileFormat("png"); // is default anyway

  //Don't draw the image yet
  $graph->SetPrintImage(0);
  //Don't output the file headers - we're inline!
  $graph->SetIsInline("1");

  $graph->SetDataType( "linear-linear");
  //First dataset to plot
  $graph->SetDataValues($balance_array);


  //Specify plotting area details
  $graph->SetImageArea(600,400);
  $graph->SetPlotType( "lines");
  $graph->SetTitleFontSize( "2");
  $graph->SetTitle( "Remaining balance");
  $graph->SetPlotAreaWorld(0,0,($current_year*12*1.1),($financing_price * 1.1));
  $graph->SetPlotBgColor( "white");
  $graph->SetPlotBorderType( "left");
  $graph->SetBackgroundColor( "white");

  //Define the X axis
  $graph->SetXLabel( "Month");
  $graph->SetHorizTickIncrement( "24");
  $graph->SetXGridLabelType( "plain");

  //Define the Y axis
  $graph->SetVertTickIncrement(($sale_price / 10));
  $graph->SetPrecisionY(0);
  $graph->SetYGridLabelType( "data");
  $graph->SetLightGridColor( "blue");

  $graph->SetDataColors( array( "red"), array( "black") );

  //Check size of tmp directory
  $tmpdirsize = dirsize($tmpdir);
  //If the size of the tmp dir has exceeded the maximum in bytes that we allow,
  //delete every file in it! We check this only once per run.
  if ($tmpdirsize > $maxbytes){
    runlink($tmpdir);
  }

  //Draw the chart
  $graph->DrawGraph();
  //Now, the image is ready - print it to the file!
  $graph->PrintImage();

  //Set correct permissions
  chmod($temp_png,0644);
  //Determine the image size in order to set it correct in the img tag below.
  $size = getimagesize ("$temp_png");
  echo "<a name=\"graphics\"><H3 align=\"center\">Graphics</H3></a><p align=\"center\"><img src=\"$temp_png\" {$size[3]}>";

}

// Compute the second image here

if (isset($principal_array["1"])){

  $graph = new PHPlot;

  //Create a file with random name in the temp dir, starting with $prefix
  $temp = tempnam($tmpdir,$prefix);
  //Add the .png ending and rename it, otherwise some web servers will not serve it.
  $temp_png = $temp . '.' . "png";
  rename($temp,$temp_png);
  //Specify this random file to be the output file of the image
  $graph->SetOutputfile($temp_png);
  $graph->SetFileFormat("png"); // is default anyway

  //Don't draw the image yet
  $graph->SetPrintImage(0);
  //Don't output the file headers - we're inline!
  $graph->SetIsInline("1");

  $graph->SetDataType( "linear-linear");
  //First dataset to plot
  $graph->SetDataValues($principal_array);


  //Specify plotting area details
  $graph->SetImageArea(600,400);
  $graph->SetPlotType( "lines");
  $graph->SetTitleFontSize( "2");
  $graph->SetTitle( "Interest vs. principal (monthly)");
  $graph->SetPlotAreaWorld(0,0,($current_year*12*1.1),($monthly_payment * 1.1));
  $graph->SetPlotBgColor( "white");
  $graph->SetPlotBorderType( "left");
  $graph->SetBackgroundColor( "white");

  //Define the X axis
  $graph->SetXLabel( "Month");
  $graph->SetHorizTickIncrement( "24");
  $graph->SetXGridLabelType( "plain");

  //Define the Y axis
  $graph->SetVertTickIncrement(($monthly_payment / 10));
  $graph->SetPrecisionY(0);
  $graph->SetYGridLabelType( "data");
  $graph->SetLightGridColor( "blue");

  $graph->SetDataColors( array("green"), array("black") );

  //Draw the first chart
  $graph->DrawGraph();

  //Second chart on the image

  //Second dataset to plot
  $graph->SetDataValues($interest_array);

  //$graph->SetDataColors( array( "blue"), array( "black") );

  //We already got them in the first graph
  $graph->SetDrawXDataLabels(0);

  //Draw a legend
  //Define the legend texts
  $graph->SetLegend(array("Principal","Interest"));
  //Set the colors for the legend texts
  $graph->SetDataColors( array( "green","blue"), array( "black","black") );
  //Draw the legend
  $graph->DrawLegend(450,175,"");

  //Set the correct color for the second chart
  $graph->SetDataColors( array( "blue"), array( "black") );
  //Draw the New data over the first chart
  $graph->DrawLines();

  //Now, the image is ready - print it to the file!
  $graph->PrintImage();

  //Set correct permissions
  chmod($temp_png,0644);
  //Determine the image size in order to set it correct in the img tag below.
  $size = getimagesize ("$temp_png");
  echo "<p align=\"center\"><img src=\"$temp_png\" {$size[3]}>";
}


function dirsize($directory){
   if (!is_dir($directory)) return -1;
   $size = 0;
   if ($DIR = opendir($directory)){
      while (($dirfile = readdir($DIR)) !== false){
         if (is_link($directory . '/' . $dirfile) || $dirfile == '.' || $dirfile == '..')
           continue;
         if (is_file($directory . '/' . $dirfile))
           $size += filesize($directory . '/' . $dirfile);
         else if (is_dir($directory . '/' . $dirfile)){
           $dirSize = dirsize($directory . '/' . $dirfile);
           if ($dirSize >= 0) $size += $dirSize;
           else return -1;
         }
      }
      closedir($DIR);
   }
   return $size;
}


function runlink($directory){
   if (!is_dir($directory)) return -1;
   if ($DIR = opendir($directory)){
      while (($dirfile = readdir($DIR)) !== false){
         if (is_link($directory . '/' . $dirfile) || $dirfile == '.' || $dirfile == '..')
           continue;
         if (is_file($directory . '/' . $dirfile))
           unlink($directory . '/' . $dirfile);
         else if (is_dir($directory . '/' . $dirfile)){
           runlink($directory . '/' . $dirfile);
         }
      }
      closedir($DIR);
   }
}

//******************************* End of  Graphics ******************************//

?>
<br><br>
<font size="-1" color="#666666">This mortgage calculator can be used to figure out monthly payments of a home mortgage loan, based on the home's sale price, the term of the loan desired, buyer's down payment percentage, and the loan's interest rate. This calculator factors in PMI (Private Mortgage Insurance) for loans where less than 20% is put as a down payment. Also taken into consideration are the town property taxes, and their effect on the total monthly mortgage payment.<br>
The calculations are visualized with the aid of two diagrams at the bottom of the page, the first one displaying the remaining balance, the second showing the interplay of monthly paid interest vs. monthly paid principal, both on a month/money coordinate system.
<br><br></font>

<p>
See the <a href="http://www.karakas-online.de/myServices/showfile.php?highlight=mortgage">source code.</a>
<!-- END BODY -->


<?
    if ($print_footer) {
        print("</body>\n");
        print("</html>\n");
    }
?>





<?
/*
    ///// mortgage_calculator.php /////
    Copyright (c) 2002 David Tufts <http://dave.imarc.net>
    Copyright (c) 2002 Graphics by Chris Karakas <http://www.karakas-online.de>
    All rights reserved.

    Redistribution and use in source and binary forms, with or without
    modification, are permitted provided that the following conditions
    are met:

    *    Redistributions of source code must retain the above copyright
     notice, this list of conditions and the following disclaimer.
    *    Redistributions in binary form must reproduce the above
     copyright notice, this list of conditions and the following
     disclaimer in the documentation and/or other materials
     provided with the distribution.
    *    Neither the name of David Tufts nor the names of its
     contributors may be used to endorse or promote products
     derived from this software without specific prior
     written permission.

    THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND
    CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES,
    INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF
    MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
    DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS
    BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL,
    EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED
    TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
    DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON
    ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY,
    OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY
    OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
    POSSIBILITY OF SUCH DAMAGE.
*/
?>

