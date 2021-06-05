<?php

namespace App\Exports;

use App\Customer;
use App\DeveloperTask;
use App\User;
use App\Task;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithEvents;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

class HubstaffActivityReport implements FromArray, ShouldAutoSize, WithHeadings, WithEvents
{
  protected $user;

  public function __construct(array $user)
  {
    $this->user = $user;

  }

  	public function registerEvents() : array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                // $event->sheet->getDelegate()->setAutoSize(true);
                $event->sheet->getDelegate()->getColumnDimension('A')->setAutoSize(true);
                $event->sheet->getDelegate()->getColumnDimension('B')->setAutoSize(true);
                $event->sheet->getDelegate()->getColumnDimension('C')->setAutoSize(true);           
                $event->sheet->getDelegate()->getColumnDimension('D')->setAutoSize(true);
                $event->sheet->getDelegate()->getColumnDimension('E')->setAutoSize(true);
                $event->sheet->getDelegate()->getColumnDimension('F')->setAutoSize(true);
                $event->sheet->getDelegate()->getColumnDimension('G')->setAutoSize(true);
            }
        ];
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function array(): array
    {
      $new_customers = [];
      // dd( $this->user );
      $totalApproved = 0;
      $totalDiff = 0;
      $totalTrack = 0;
        // dd( $this->user );
      foreach ($this->user as $key => $user) {
      	// foreach($user['tasks'] as $key =>  $ut) {
	      	
	      	// @list($taskid,$devtask,$taskName,$estimation,$status,$devTaskId) = explode("||",$ut);
            if ($user['is_manual']) {
                $task = DeveloperTask::where('id', $user['task_id'])->first();
                if ($task) {
                    $taskSubject = '#DEVTASK-' . $task->id . '-' . $task->subject;
                } else {
                    $task = Task::where('id', $ar->task_id)->first();
                    if ($task) {
                        // $estMinutes = ($task->estimate_minutes && $task->estimate_minutes > 0) ? $task->estimate_minutes : "N/A";
                        // $taskSubject = $ar->task_id . '||#TASK-' . $task->id . '-' . $task->task_subject."||#TASK-$task->id||$estMinutes||$task->status||$task->id";
                        $taskSubject = '#TASK-' . $task->id . '-' . $task->task_subject;
                    }
                }
            } else {
                $task = DeveloperTask::where('hubstaff_task_id', $user['task_id'])->orWhere('lead_hubstaff_task_id', $user['task_id'])->first();
                if ($task && empty( $task_id )) {
                    // $estMinutes = ($task->estimate_minutes && $task->estimate_minutes > 0) ? $task->estimate_minutes : "N/A";
                    $taskSubject = '#DEVTASK-' . $task->id . '-' . $task->subject;
                } else {
                    $task = Task::where('hubstaff_task_id', $user['task_id'])->orWhere('lead_hubstaff_task_id', $user['task_id'])->first();
                    if ($task && empty( $developer_task_id )) {
                        // $estMinutes = ($task->estimate_minutes && $task->estimate_minutes > 0) ? $task->estimate_minutes : "N/A";
                        $taskSubject = '#TASK-' . $task->id . '-' . $task->task_subject;
                    }
                }
            }
            $devTask = $task;
            // $devTask = DeveloperTask::where('hubstaff_task_id', $user['task_id'])->first();

            if( empty( $devTask ) ){
                continue;
            }
            $userDev = User::find($user['user_id']);
            // $task = \App\Hubstaff\HubstaffActivity::where('task_id', $user['task_id'])->first();
            // dd( $devTask );
	      	$trackedTime = \App\Hubstaff\HubstaffActivity::where('task_id', $user['task_id'])->sum('tracked');
	      	$time_history = \App\DeveloperTaskHistory::where('developer_task_id',$devTask->id)->where('attribute','estimation_minute')->where('is_approved',1)->first();

	      	if($time_history) {
                $est_time = $time_history->new_value;
            }
            else {
                $est_time = 'N/A';
            }

            if (is_numeric($devTask->estimate_minutes) && $trackedTime && $devTask->subject){
                $diff =  $devTask->estimate_minutes - number_format($trackedTime / 60,2,".",",");
            }else{
                $diff = 'N/A';
            }

            if( $devTask ){
    	        $new_customers[$key]['User'] = $userDev->userName ?? null;
                $new_customers[$key]['date'] = \Carbon\Carbon::parse($user['date'])->format('d-m');
    	        $new_customers[$key]['TaskId'] = $taskSubject;
    	        $new_customers[$key]['TimeAppr'] = $est_time;
    	        $new_customers[$key]['TimeDiff'] = $diff;
    	        $new_customers[$key]['TimeTracked'] =  ( $trackedTime && $devTask->subject) ? number_format($trackedTime / 60,2,".",",") : 'N/A';
    	        $new_customers[$key]['status'] = $devTask->status;
    	        

    	        if (is_numeric($est_time) && $devTask->subject) {
    	        	$totalApproved += $est_time;
    	        }

    	        if (is_numeric($diff) && $devTask->subject) {
    	        	$totalDiff += $diff;
    	        }
    	        if ($trackedTime && $devTask->subject) {
    	        	 $totalTrack += $trackedTime;
    	        }
            }

      	// }
      }

      array_push($new_customers, [null,null,null,null,null, null]);
      array_push($new_customers, ['Total ',null,null,$totalApproved,$totalDiff, number_format($totalTrack / 60,2,".",",")]);
      // dd( $new_customers );
      return $new_customers;
    }

    public function headings() : array
    {
        return ["User", "Date", "Task", "Time approved", "Time Diff", "Time tracked", "Status"];
    }
}
