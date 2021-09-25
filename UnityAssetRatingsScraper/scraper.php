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
	$assetStoreUrl_productName_reviews = 'https://assetstore.unity.com/packages/essentials/asset-packs/standard-assets-32351/reviews';
	
	?>
	
	<h3>Product Name Here (<a href="<?php print_r($assetStoreUrl_productName); ?>" target="assetStore">Asset Store Page</a>)</h3>
	<?php
	print(FetchRatingString($assetStoreUrl_productName));
	print(FetchReviewString($assetStoreUrl_productName_reviews));
	?>
	<hr>

	<?php 
	function FetchRatingString($pageUrl)
	{
		
		$page = file_get_contents($pageUrl);
		$ratingString = "<ul>";

		// From first possible star to last possible star is about 400 characters of page source. Too many chars here, and 
		// you run the risk of detected "featued review" stars, or stars of other products in the suggested product listing.
		$starTokenZoneLength = 400;
		$fullStarToken = "ifont-star";
		$outlineStarToken = "ifont-star-outline";
		$fullStarChar = "★";
		$outlineStarChar = "☆";
		
		// Star Rating //
		//
		// Stars now have no tool tip, but use ifont (as of December 2019 store page style refresh):
		// e.g. <div class="_3IZbW ifont ifont-star edpRo" data-reactid="242"></div> for full star
		// e.g. <div class="_3IZbW ifont ifont-star-outline _3xV6B edpRo" data-reactid="231"></div> for outline (no) star

		// Approximate start position of the star rating area (for efficiency)
		$roughStarStartPosition = strpos($page, $fullStarToken);
		$fullStarCount = substr_count($page, $fullStarToken, $roughStarStartPosition, $starTokenZoneLength);
		$outlineStarCount = substr_count($page, $outlineStarToken, $roughStarStartPosition, $starTokenZoneLength);
		$totalStarCount = $fullStarCount - $outlineStarCount;

		$starString = "";

		for ($x = 0; $x < 5; $x++) {

			if($x < $totalStarCount)
			{
				$starString .= $fullStarChar;
			}
			else
			{
				$starString .= $outlineStarChar;
			}

		}

		$ratingString .= '<li>' . $starString . '  (' . $totalStarCount . ' out of 5)';
		
		// User Rating Count //
		//
		// As of December 2019 store page style refresh, reviews are no longer formatted "N user reviews". It is now just "N Reviews"
		//
		// As of September 2021 store page style refresh, reviews are no longer formatter "N Reviews". It is just stars and (N) where N is number of user ratings

		// Look for a number in parentheses
		$userReviewCountPattern = '/\(([\d]*)\)/';
		// And extend breadth of substring search to account for stars plus some "(N)"
		$reviewCountTokenZoneExtendedLength = $starTokenZoneLength + 20;
		$reviewCountSubstring = substr($page, $roughStarStartPosition, $reviewCountTokenZoneExtendedLength);
		preg_match($userReviewCountPattern, $reviewCountSubstring, $ratingCountResults);
		$scrubbedRatingsCount = str_replace(array('(', ')'), '', $ratingCountResults[0]);
		$ratingString .= ' (' . $scrubbedRatingsCount. ' User Ratings)</li>';

		return $ratingString;
		
	}
	?>

	<?php 
	function FetchReviewString($pageUrl)
	{
				
		$page = file_get_contents($pageUrl);
		$reviewString = "";

		// For review count, we use the star rating followed by string such as "N User Reviews", about 395 chars
		$postStarCharactersToReviewCount = 600;
		$fullStarToken = "ifont-star";
		
		// User Reviews //
		// Approximate start position of the star rating area (for efficiency)
		$roughStarStartPosition = strpos($page, $fullStarToken);
		$reviewCountSubtstring = substr($page, $roughStarStartPosition, $postStarCharactersToReviewCount);
		
		$userReviewCountPattern = '/([\d]*) User Reviews/';
		preg_match($userReviewCountPattern, $reviewCountSubtstring, $userReviewCountResults);
		$reviewString .= '<li>' . $userReviewCountResults[0] . '</li>';
		$reviewString .= "</ul>";
		
		return $reviewString;
		
	}
	?>

	
	</body>
</html>