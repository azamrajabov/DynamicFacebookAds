<?php
/**
 * Created by IntelliJ IDEA.
 * User: azam
 * Date: 14/07/2018
 * Time: 19:51
 */

namespace DynamicFbAds\Services;

use DynamicFbAds\Repositories\RepositoryInterface;

class IncentivesService
{
    public $incentivesRepository;
    private $incentives = null;
    private $offers = null;

    public function __construct(RepositoryInterface $repository)
    {
        $this->incentivesRepository = $repository;
    }

    public function getIncentives($websiteId, $vehicle, $fields)
    {
        if ($this->incentives == null) {
            // get items from repository
            $items = $this->incentivesRepository->getWebsiteIncentives($websiteId);
            if (!$items) {
                $this->incentives = [];
                return [];
            }
            foreach ($items as $incentive) {
                $incentive['Value'] = (int) str_replace("-", "", $incentive['Value']);
                $this->incentives[] = (array) $incentive;
            }
        }
        if (!$this->incentives) {
            return [];
        }

        return array_filter($this->incentives, function ($incentive) use ($vehicle, $fields) {
            foreach ($fields as $field) {
                if ($vehicle[$field] != $incentive[$field]) {
                    return false;
                }
            }
            return true;
        });
    }

    public function getOffers($websiteId, $vehicle, $fields, $minTerm = 36)
    {
        $offers = [];
        $fieldValues = [];
        foreach ($fields as $field) {
            $fieldValues['wv.' . $field] = $vehicle[$field];
        }
        $items = $this->incentivesRepository->getWebsiteOffersByFields($websiteId, $fieldValues, $minTerm);
        if (!$items) {
            return [];
        }
        foreach ($items as $offer) {
            if (in_array($offer['Term'], array_column($offers, "Term"))) {
                // make unique Term
                continue;
            }
            $offers[] = (array) $offer;
        }
        return $offers;
    }
}
