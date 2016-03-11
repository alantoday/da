<?php include("includes_my/header.php"); ?>

<?php echo MyWriteMidSection("LEADERBOARD", "Who's Climbing The Fastest?",
	"We teach digital entrepreneurs how to start and grow a profitable business with our unique products and live events.",
	"PROMOTE NOW","/dashboard/scale",
	"CALL COACH", "/my-coach"); ?>
<?php include("blank_menu.php"); ?>
<?php echo MyWriteMainSectionTop(30); ?>       

<div class="divtable">
    <div class="divrow">
		<div class="divcell">
<p style='font-size:16px;'>Last 7 Days Top Income Earners</p>
<?php echo _GetLeaderboard($db, date("Y-m-d", strtotime("last week")),date("Y-m-d")); ?>
		</div>
        <div class="divcell">
<p style='font-size:16px;'>Last 30 Days Top Income Earners</p>
<?php echo _GetLeaderboard($db, date("Y-m-d", strtotime("30 days ago")),date("Y-m-d")); ?>
		</div>
        <div class="divcell">
<p style='font-size:16px;'>Last 6 Months Top Income Earners</p>
<?php echo _GetLeaderboard($db, date("Y-m-d", strtotime("6 months ago")),date("Y-m-d")); ?>
		</div>
    </div>
</div>

<?php
function _GetLeaderboard($db, $from_date, $to_date) {
	# NOTE: This crons updates commissions_daily table: crons/calc_comms_daily.php
	
# List commissions earned by a member
	$query = "SELECT m.member_id, m.email, m.name, m.gravatar, SUM(c.commissions) as sum_commissions
				FROM commissions_daily c
				JOIN members m ON m.member_id = c.member_id
				WHERE m.member_id > 104
				AND m.member_id NOT IN (108,109,110)
				AND c.create_date BETWEEN '$from_date' AND '$to_date'
				GROUP BY m.member_id
				ORDER BY sum_commissions DESC
				LIMIT 5";
				// Add in date ranges
	$result = mysqli_query($db, $query) or die(mysqli_error($db).". $query");
	$table_rows = false;
	for($i=1; $row = mysqli_fetch_assoc($result); $i++) {
		$table_rows .= "<tr>"
		. WriteTD($i)	
		. WriteTD("<div style='padding-right:10px;padding-top:5px;'>".$row['name']."</div>")	
		. WriteTD("<div style='align:right;width:30px;height:30px;background-size:cover;background-position:center center;border-radius:30px;background:url(\"".get_gravatar($row['gravatar'], 30)."\")'></div>")	
		. "</tr>";
	}
	
	if (!$table_rows) {
		$table_rows = "<tr><td colspan=3>There are no members with commissions over that timeframe.</td></tr>";
	}
	$table_foot = "</table>";
	$table_head = "<table width='300px' class='daTable'><tr><thead>"
	. WriteTH("Rank")	
	. WriteTH("Member")	
	. WriteTH("")	
	. "</tr></thead>";
	$res = $table_head . $table_rows . $table_foot;
	return $res;
}
?>
                 
<?php include("includes_my/footer.php"); ?>