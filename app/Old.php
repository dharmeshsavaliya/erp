<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Old extends Model
{
    protected $primaryKey = 'serial_no';
   /**
     * Fillables for the database
     *
     * @access protected
     *
     * @var array $fillable
     */
    protected $fillable = array(
        'name', 'description', 'amount',
        'commitment', 'communication',
        'status'
    );

    /**
     * Protected Date
     *
     * @access protected
     * @var    array $dates
     */
    protected $dates = [
        'created_at',
        'updated_at',
    ];

    /**
     * Saving categories
     *
     * @param string $request Request attributes
     *
     * @return \Illuminate\Http\Response
     */
    public function saveRecord($request)
    {
        if (!empty($request)) {
            $this->name = filter_var($request['name'], FILTER_SANITIZE_STRING);
            $this->description = filter_var($request['description'], FILTER_SANITIZE_STRING);
            $this->amount = filter_var($request['amount'], FILTER_SANITIZE_STRING);
            $this->commitment = filter_var($request['commitment'], FILTER_SANITIZE_STRING);
            $this->communication = filter_var($request['communication'], FILTER_SANITIZE_STRING);
            $this->status = filter_var($request['status'], FILTER_SANITIZE_STRING);
            $this->email = filter_var($request['email'], FILTER_SANITIZE_STRING);
            $this->number = filter_var($request['number'], FILTER_SANITIZE_STRING);
            $this->address = filter_var($request['address'], FILTER_SANITIZE_STRING);
            $this->save();
            return 'sucess';
        }
    }

    /**
     * Saving categories
     *
     * @param string $request Request attributes
     *
     * @return \Illuminate\Http\Response
     */
    public function updateRecord($request, $serial_no)
    {
        if (!empty($request) || !empty($serial_no)) {
            $incoming = self::findOrFail($serial_no);
            $incoming->name = filter_var($request['name'], FILTER_SANITIZE_STRING);
            $incoming->description = filter_var($request['description'], FILTER_SANITIZE_STRING);
            $incoming->amount = filter_var($request['amount'], FILTER_SANITIZE_STRING);
            $incoming->commitment = filter_var($request['commitment'], FILTER_SANITIZE_STRING);
            $incoming->communication = filter_var($request['communication'], FILTER_SANITIZE_STRING);
            $incoming->status = filter_var($request['status'], FILTER_SANITIZE_STRING);
            $incoming->email = filter_var($request['email'], FILTER_SANITIZE_STRING);
            $incoming->number = filter_var($request['number'], FILTER_SANITIZE_STRING);
            $incoming->address = filter_var($request['address'], FILTER_SANITIZE_STRING);
            $incoming->save();
            return 'sucess';
        }
    }

    /**
     * Get Status
     *
     * @return \Illuminate\Http\Response
     */
    public static function getStatus()
    {
        $types = array(
            'pending'  => 'pending',
            'disputed' => 'disputed',
            'settled'  => 'settled',
            'paid'     => 'paid',
            'closed'  => 'closed',
        );
        return $types;
    }

}
