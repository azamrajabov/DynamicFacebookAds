<?php
/**
 * Created by IntelliJ IDEA.
 * User: azam
 * Date: 14/06/2018
 * Time: 13:11
 */

namespace DynamicFbAds\Repositories;

use DynamicFbAds\Library\Pocket;

/**
 * Class DealerWebsitesRepository
 *
 * @package DynamicFbAds\Repositories
 */
class IncentivesRepository extends AbstractRepository implements RepositoryInterface
{
    public function getWebsiteIncentives($websiteId)
    {
        return $this->db->query(
            "SELECT
              `self`.`Id`,
              `wv`.`Year`,
              `wv`.`Make`,
              `wv`.`Model`,
              `wv`.`Trim`,
              `self`.`PromotionType`,
              `self`.`FinanceMethod`,
              IFNULL(
                dc.ConditionCategory, self.ConditionCategory
              ) AS `ConditionCategory`,
              `self`.`PromotionName`,
              `self`.`PromotionDescription`,
              `self`.`EffectiveDate`,
              `self`.`ExpirationDate`,
              `ai`.`Value`,
              `ai`.`vuid`
            FROM 
              `dfi_incentive` AS `self`
              INNER JOIN `dfi_applied_incentives` AS `ai` ON ai.IncentiveId = self.Id
              INNER JOIN `c_website_vehicles` AS `wv` ON ai.vuid = wv.VUID AND wv.website_id = ai.websiteId
              LEFT JOIN `dfi_incentive_dealer_config` AS `dc` ON dc.IncentiveId = self.Id
              AND dc.DealerId = wv.dealer_id
            WHERE
              (wv.website_id = ?)
              AND (self.PromotionType = 1)
            GROUP BY
              `wv`.`Year`,
              `wv`.`Make`,
              `wv`.`Model`,
              `wv`.`Trim`,
              `self`.`Id`
            ORDER BY
              `ai`.`Value` ASC",
            $websiteId
        )->fetchAll();
    }

    public function getWebsiteOffersByFields($websiteId, $fields, $minTerm = 36)
    {
        return $this->db->query(
            "SELECT
              `wv`.`Year`,
              `wv`.`Make`,
              `wv`.`Model`,
              `wv`.`Trim`,
              `wv`.`Body`,
              `wv`.`StyleID` AS `StyleId`,
              `self`.`PromotionType`,
              `self`.`FinanceMethod`,
              `self`.`PromotionName`,
              `self`.`PromotionDescription`,
              `self`.`EffectiveDate`,
              `self`.`ExpirationDate`,
              `io`.`Term`,
              `io`.`TermStart`,
              `io`.`Apr`,
              `io`.`CustomerCash`
            FROM
              `dfi_incentive` AS `self`
              INNER JOIN `dfi_incentive_offer` AS `io` ON io.IncentiveId = self.Id
                AND io.term >= ?
              INNER JOIN `dfi_incentive_vehicle` AS `iv` ON iv.IncentiveId = self.Id
              INNER JOIN `c_website_vehicles` AS `wv` ON wv.StyleId = iv.StyleId
                AND wv.website_id = ?
            WHERE
            (self.PromotionType = 2)
            AND (self.FinanceMethod = 1)
            AND (io.Apr >= 0) AND 
            %and
            GROUP BY
              io.term, io.CustomerCash
            ORDER BY
              `io`.`Term` DESC,
              `io`.`CustomerCash` DESC,
              `io`.`Apr` ASC",
            $minTerm,
            $websiteId,
            $fields
        )->fetchAll();
    }
}
