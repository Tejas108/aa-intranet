<?php
/*
Template Name: Magic Number
*/
?>
<?php get_header(); ?>

	<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
		<article class="post" id="post-<?php the_ID(); ?>">
		  <h2><?php the_title(); ?></h2>
      <?php
      global $wpdb;
      $currentDate = date("Y-m-d");
      $today = date('l \t\h\e jS');
      $staffNumber = 20;
      $results = $wpdb->get_row( "SELECT * from wp_magic_number WHERE date = '".$currentDate."' ORDER BY date", ARRAY_A );

      if($results[date] !== $currentDate) {
	      $results[lunch] = 0;
          $results[dinner] = 0;
      } else {
	      $results[lunch] -= 20;
          $results[dinner] -= 20;
      }

      if(current_user_can('office') || current_user_can('administrator')){ ?>
        <p>Enter the number of guests, <em>not including staff</em>, for lunch and dinner today.</p>
        <form id="number-form">
	        <?php if($results[lunch] === 20){ ?>
		        Lunch Count: <input type="text" name="lunch" value="20" style="width: 40px;">
	        <?php } else { ?>
		        Lunch Count: <input type="text" name="lunch" value="<?php echo $results[lunch] ?>" style="width: 40px;">
	        <?php } ?>
            <?php if($results[dinner] === 20){ ?>
                Dinner Count: <input type="text" name="dinner" value="20" style="width: 40px;">
            <?php } else { ?>
                Dinner Count: <input type="text" name="dinner" value="<?php echo $results[dinner] ?>" style="width: 40px;">
            <?php } ?>
            <label for="meal-note">Note: </label><textarea cols="20" rows="5" type="text" name="note" id="note"><?php echo $results[note] ?></textarea>
          <input type="hidden" name="action" value="magic_number"/>
          <input type="submit" name="submit-number" id="submit-number" value="Enter">
        </form>
	      <?php
      }

      if($results[lunch] > 0) {
          $lunchNumber = $results[lunch] + 20;
      }else {
          $lunchNumber = 20;
      }
      if($results[dinner] > 0) {
          $dinnerNumber = $results[dinner] + 20;
      }else {
          $dinnerNumber = 20;
      }

      echo "<p>The lunch count is <strong>" . $lunchNumber . " </strong></p>";
      echo "<p>The dinner count is <strong>" . $dinnerNumber . " </strong></p>";
		if(current_user_can('office') || current_user_can('hrc') || current_user_can('board') || current_user_can('administrator')){ ?>
				<button class="button show-all-dates" style="font-size: 1em;">Show all Dates</button>
		<?php }
		global $wpdb;
      $getAllResults = $wpdb->get_results( "SELECT * from wp_magic_number ORDER BY date DESC", ARRAY_A );
      ?>
			<div class="all-numbers-container">
				<table>
					<tr>
						<th>Date</th>
						<th>Lunch Guests</th>
                        <th>Dinner Guests</th>
					</tr>
				<?php
					foreach($getAllResults as $row) {
						$row[lunch] -= $staffNumber;
                        $row[dinner] -= $staffNumber;
						echo "<tr>";
						echo "<td>".$row[date]."</td>";
						echo "<td>".$row[lunch]."</td>";
                        echo "<td>".$row[dinner]."</td>";
						echo "</tr>";
					}
				?>
					</tr>
				</table>
			</div>

</article>
		<?php endwhile; endif; ?>
<?php get_footer(); ?>
