<?php
/*
Template Name: Welcome Page
*/
?>
<?php get_header(); ?>

<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
  <article class="post" id="post-<?php the_ID(); ?>">
    <!-- <h2><?php the_title(); ?></h2> -->
    <div class="entry">


      <?php
      global $wpdb;
      $currentDate = date("Y-m-d");
      $results = $wpdb->get_row("SELECT * from wp_magic_number WHERE date = '" . $currentDate . "' ORDER BY date", ARRAY_A);
      $submittedAt = $results[submittedAt];
      $note = $results[note];
      if (!$results[lunch]) {
        $lunch = 20;
      } else {
        $lunch = $results[lunch];
      }
      if (!$results[dinner]) {
        $dinner = 20;
      } else {
        $dinner = $results[dinner];
      }
      ?>
      <h3>News</h3>
      <div class="home-news-item">
        <?php $posts = get_posts("category=48&numberposts=X"); ?>
        <?php if ($posts) : ?>
          <?php foreach ($posts as $post) : setup_postdata($post); ?>
            <div class="post">
              <?php the_content(); ?>

            </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
<!--      <h3>Today's Magic Number</h3>-->
<!--      <small>(Lunch and Dinner Today)</small>-->
<!--      <div class="magic-number-container">-->
<!--        <div class="magic-number-content">-->
<!--          <div>-->
<!--            <span class="num"><strong>--><?php //echo $lunch; ?><!--</strong></span><br/>-->
<!--            Lunch-->
<!--          </div>-->
<!--        </div>-->
<!--        <div class="magic-number-content">-->
<!--          <div>-->
<!--            <span class="num"><strong>--><?php //echo $dinner; ?><!--</strong></span><br/>-->
<!--            Dinner-->
<!--          </div>-->
<!--        </div>-->
<!--        <small style="clear:both;">-->
<!--          <p>--><?php //if ($note) {
//              echo "NOTE: " . $note;
//            } ?><!--</p>-->
<!--          --><?php //$message = $submittedAt ? 'Updated At: ' : '';
//          echo $message . $submittedAt; ?>
<!--        </small>-->
<!--      </div>-->
<!--      <div class="home-left">-->
<!--        <h3>This Month's Events</h3>-->
<!--        <div id="divContainer">-->
<!--          <div id="frameContainer">-->
<!--            <iframe src="http://anandaashram.org/events-calendar" scrolling="no" kwframeid="1">-->
<!--            </iframe>-->
<!--          </div>-->
<!--        </div>-->
<!--      </div>-->
    </div>
    <div class="home-right">
      <h3>Directory</h3>
      <div class="directory border-box">
        <table>
          <tr colspan="2"><strong>Ashram</strong></tr>
          <tr>
            <td valign="top">Main Office</td>
            <td valign="top"><a href="tel:8457825575">(845) 782-5575</a></td>
          </tr>
          <tr>
            <td>Main Office FAX</td>
            <td><a href="tel:8457747368">(845) 774-7368</a></td>
          </tr>
          <tr>
            <td>Kitchen</td>
            <td><a href="tel:8457823718">(845) 782-3718</a></td>
          </tr>
          <tr>
            <td>Gift Shop</td>
            <td><a href="tel:8457823245">(845) 782-3245</a></td>
          </tr>
        </table>
        <table>
          <tr colspan="2"><strong>Bus / Taxi</strong></tr>
          <tr>
            <td valign="top">Shortline Bus (NYC)</td>
            <td valign="top"><a href="tel:2127364700">(212) 736-4700</a> | <a href="tel:6318405">(800) 631-8405</a></td>
          </tr>
          <tr>
            <td>Monroe Taxi</td>
            <td><a href="tel:8457828141">(845) 782-8141</a></td>
          </tr>
          <tr>
            <td>Beam's Taxi</td>
            <td><a href="tel:8457834444">(845) 783-4444</a></td>
          </tr>
          <tr>
            <td>Village Taxi</td>
            <td><a href="tel:8457836112">(845) 783-6112</a></td>
          </tr>
        </table>
        <table>
          <tr colspan="2"><strong>Emergency</strong></tr>
          <tr>
            <td>State Police</td>
            <td><a href="tel:8457828311">(845) 782-8311</a></td>
          </tr>
          <tr>
            <td>Monroe Police</td>
            <td><a href="tel:8457828644">(845) 782-8644</a></td>
          </tr>
          <tr>
            <td>Harriman Police</td>
            <td><a href="tel:8457826644">(845) 782-6644</a></td>
          </tr>
          <tr>
            <td>Monroe Ambulance</td>
            <td><a href="tel:8457834545">(845) 783-4545</a></td>
          </tr>
          <tr>
            <td>Orange Regional, Middletown</td>
            <td><a href="tel:8453331000">(845) 333-1000</a></td>
          </tr>
          <tr>
            <td>Good Samaritan Hospital, Suffern</td>
            <td><a href="tel:8453685000">(845) 368-5000</a></td>
          </tr>
          <tr>
            <td>First Care, Monroe</td>
            <td><a href="tel:8457836333">(845) 783-6333</a></td>
          </tr>
          <tr>
            <td>Harriman Fire Department</td>
            <td><a href="tel:8457831120">(845) 783-1120</a></td>
          </tr>
          <tr>
            <td>Monroe Fire Department</td>
            <td><a href="tel:8457836791">(845) 783-6791</a></td>
          </tr>
        </table>

      </div>
    </div>
  </article>
<?php endwhile; endif; ?>
<?php get_footer(); ?>
