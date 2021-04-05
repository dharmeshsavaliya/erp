<?php

namespace App;
/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
use Illuminate\Database\Eloquent\Model;

class Scraper extends Model
{
    /**
     * @var string
     * @SWG\Property(property="supplier_id",type="integer")
     * @SWG\Property(property="parent_supplier_id",type="integer")
     * @SWG\Property(property="scraper_name",type="string")
     * @SWG\Property(property="scraper_type",type="string")
     * @SWG\Property(property="scraper_total_urls",type="string")
     * @SWG\Property(property="scraper_new_urls",type="string")
     * @SWG\Property(property="scraper_existing_urls",type="string")

     * @SWG\Property(property="scraper_start_time",type="datetime")
     * @SWG\Property(property="scraper_logic",type="string")
     * @SWG\Property(property="scraper_made_by",type="string")
     * @SWG\Property(property="scraper_priority",type="string")
     * @SWG\Property(property="next_step_in_product_flow",type="string")
     * @SWG\Property(property="status",type="string")
     * @SWG\Property(property="has_sku",type="string")
     * @SWG\Property(property="last_completed_at",type="datetime")
     * @SWG\Property(property="last_started_at",type="datetime")
     */

    const STATUS = [
        ''                        => "N/A",
        'Ok'                      => 'Ok',
        'Rework'                  => 'Rework',
        'In Process'              => 'In Process',
        'Scrapper Fixed'          => 'Scrapper Fixed',
        'Process Complete'        => 'Process Complete',
        'Categories'              => 'Categories',
        'Logs Checked'            => 'Logs Checked',
        'Scrapper Checked'        => 'Scrapper Checked',
        'All brands Scrapped'     => 'All brands Scrapped',
        'All Categories Scrapped' => 'All Categories Scrapped'
    ];

    protected $fillable = [
        'supplier_id', 'parent_supplier_id', 'scraper_name', 'scraper_type', 'scraper_total_urls', 'scraper_new_urls', 'scraper_existing_urls', 'scraper_start_time', 'scraper_logic', 'scraper_made_by', 'scraper_priority', 'inventory_lifetime', 'next_step_in_product_flow', 'status', 'last_completed_at', 'last_started_at','flag','developer_flag'];

    public function scraperMadeBy()
    {
        return $this->hasOne('App\User', "id", "scraper_made_by");
    }

    public function scraperParent()
    {
        return $this->hasOne('App\Scraper', "supplier_id", "parent_supplier_id");
    }

    public function supplier()
    {
        return $this->hasOne('App\Scraper', "id", "supplier_id");
    }

    public function mainSupplier()
    {
        return $this->hasOne('App\Supplier', "id", "supplier_id");
    }

    public function mapping()
    {
        return $this->hasMany('App\ScraperMapping', "scrapers_id", "id");
    }

    public function parent()
    {
        return $this->hasOne('App\Scraper', 'id', 'parent_id');
    }

    public function getChildrenScraper($name)
    {
        $scraper              = $this->where('scraper_name', $name)->first();
        return $parentScraper = $this->where('parent_id', $scraper->id)->get();
    }

    public function getChildrenScraperCount($name)
    {
        $scraper              = $this->where('scraper_name', $name)->first();
        return $parentScraper = $this->where('parent_id', $scraper->id)->count();
    }

    public function getScrapHistory()
    {
        return $this->hasMany('App\ScrapRequestHistory', 'scraper_id', 'id')->orderBy('updated_at', 'desc')->take(20);
    }

    public function scraperRemark()
    {
        return \App\ScrapRemark::where("scraper_name", $this->scraper_name)->latest()->first();
    }

    public function developerTask($id)
    {
        return \App\DeveloperTask::where("scraper_id", $id)->first();
    }
}
