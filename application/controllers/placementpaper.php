<?php

/**
 * Description of placementpaper
 *
 * @author Faizan Ayubi
 */
class PlacementPaper {

    public function index() {
        $seo = Framework\Registry::get("seo");

        $seo->setTitle("Companies Placement Papers, Experiences");
        $seo->setKeywords("placement papers");
        $seo->setDescription("Browse through thousands of placement papers and experiences shared by thousands of student across india");

        $this->getLayoutView()->set("seo", $seo);
    }

}
