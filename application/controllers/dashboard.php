<?php
class Dashboard extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('UserModel');
		$this->load->model('TDriverModel');
	}

	
	public function everyone_locations()
	{
		// die;
		$users = $this->UserModel->getUserLocations();
		$tdrivers = $this->TDriverModel->getTDriverLocations();
		$rideRequestPolls = $this->UserModel->getRideRequestPolls();
		$tempArr=array();

		// print_r($rideRequestPolls);
		// return;
		//BEGIN AGGREGATING RIDES 
		$drivers = array();
		$rideIdWhichStatusIsOne=null;
		$rideObject=null;
		$aggregatedRides = array();
		for($i = 0 ; $i<count($rideRequestPolls); $i++){
			if($rideRequestPolls[$i]->status == 1){
				if($rideIdWhichStatusIsOne==null){
					$rideObject = $rideRequestPolls[$i];
					$rideIdWhichStatusIsOne = $rideRequestPolls[$i]->ride_id;
					$rideObject->assigned_tdrivers=array();
					if($rideRequestPolls[$i]->tdriver_id!=null)
						array_push($rideObject->assigned_tdrivers, (object)array("tdriver_id"=>$rideRequestPolls[$i]->tdriver_id));
					unset($rideObject->tdriver_id);
				}else{
					//from one state-1 ride to another state-1 ride
					if($rideIdWhichStatusIsOne!=$rideRequestPolls[$i]->ride_id){
						array_push($aggregatedRides, $rideObject);

						$rideObject = $rideRequestPolls[$i];
						$rideIdWhichStatusIsOne = $rideRequestPolls[$i]->tdriver_id;
						$rideObject->assigned_tdrivers=array();
						if($rideRequestPolls[$i]->tdriver_id!=null)
							array_push($rideObject->assigned_tdrivers, (object)array("tdriver_id"=>$rideRequestPolls[$i]->tdriver_id));
						unset($rideObject->tdriver_id);
					}else{
						array_push($rideObject->assigned_tdrivers, (object)array("tdriver_id"=>$rideRequestPolls[$i]->tdriver_id));
					}
				}
			}else{
				//from state-1 to non state-1 ride
				if($rideIdWhichStatusIsOne!=null){
					array_push($aggregatedRides, $rideObject);
					$rideIdWhichStatusIsOne=null;
					$rideObject=null;
				}
				if($rideRequestPolls[$i]->tdriver_id!=null)
					$rideRequestPolls[$i]->assigned_tdrivers=array((object)array("tdriver_id"=>$rideRequestPolls[$i]->tdriver_id));
				else // there are no assigned drivers yet.
					$rideRequestPolls[$i]->assigned_tdrivers = array();
				unset($rideRequestPolls[$i]->tdriver_id);
				array_push($aggregatedRides, $rideRequestPolls[$i]);
			}
		}
		if($rideIdWhichStatusIsOne!=null){
			array_push($aggregatedRides, $rideObject);
			$rideIdWhichStatusIsOne=null;
			$rideObject=null;
		}
		// print_r($aggregatedRides);
		//END AGGREGATING RIDES 

		$res = (object)array("users"=>$users, "tdrivers"=>$tdrivers, "rides"=>$aggregatedRides);
		// print_r($locations);
		$contents = $this->output
		                  ->set_content_type('application/json')
		                  ->set_output(json_encode($res));
		 // echo json_encode($data['tdrivers_locations']);
	}
}
?>