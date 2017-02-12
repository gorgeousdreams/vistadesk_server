<?php
class CashflowPlanItem extends AppModel
{
	use MultiTenantTrait;
	protected $fillable = array('description', 'category', 'amount', 'start_date', 'end_date', 'recurrence');

	public function cashflowPlanItems() {
		return $this->hasMany('CashflowPlanItem', 'item_id');
	}

	public function createEntries() {
		DB::update(DB::raw("delete from cashflow_plan_entries where item_id = ?"), array($this->id));

		$occurences = array();

		$nextDate = $this->start_date;
		while ($nextDate < $this->end_date) {
			$occurences[] = $nextDate;

			if ($this->recurrence == 'monthly') {
				$nextDate = date('Y-m-d', strtotime($nextDate . " +1 month"));
			}
		}

		foreach($occurences as $day) {
			$entry = new \CashflowPlanEntry(array(
				'item_id' => $this->id,
				'description' => $this->description,
				'amount' => $this->amount,
				'day' => $day
				));
			$entry->save();
		}

	}

}
