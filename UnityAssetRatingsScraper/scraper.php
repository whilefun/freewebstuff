<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>

	<head>
	<title>Unity Asset Ratings Scraper</title>
	</head>
	<body>

	<h1>Ratings Scraper</h1>
	<hr>

	<?php
	
	// List your product(s) here
	$assetStoreUrl_productName = 'https://assetstore.unity.com/packages/essentials/asset-packs/standard-assets-32351';
	
	?>
	
	<h3>Product Name Here<a href="<?php print_r($assetStoreUrl_productName); ?>" target="assetStore">(Asset Store Page)</a></h3>
	<?php
	print(FetchRatingString($assetStoreUrl_productName));
	?>
	<hr>
		
	<?php 
	function FetchRatingString($pageUrl)
	{
				
		$page = file_get_contents($pageUrl);
		$ratingString = "<ul>";
		
		// Star Rating
		$starRating = '/(Rated [\d] stars out of 5 stars [(][\d]* user ratings[)])/';
		preg_match($starRating, $page, $rating);
		$ratingString .= '<li>' . $rating[0] . '</li>';

		// User Review Count
		$userReviewCount = '/([\d]* user reviews)/';
		preg_match($userReviewCount, $page, $reviews);
		$ratingString .= '<li>' . $reviews[0] . '</li>';
		
		$ratingString .= "</ul>";
		return $ratingString;
		
	}
	?>

	</body>
</html>