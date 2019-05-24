<?php

/*

    this is a dynamic article action, which is launched either by
    Apiaction.php (component)
    Updatevariables (api method)
    Refreshaction (api method)

    If its called by either api method, the object is passed on to Apiaction.php eventually.

    Either RenderData or DynamicUpdate are called

    It should return json which gets put into the layoutconfig

    Data saving on picture submit is little complicated, because we upload
    async from the client. So once user has submitted a photo, we launch
    an async process to deal with that and to eventually add it to the action.
    Process is not perfect, as we rely on temporary variable values that might
    get overwritten if user uploads two photos very quickly after one another.

*/

class MobilequizModel {

    public static function getBranchInfo($playid,$branchid,$poinsystem='secondary'){
        $branchstats = Aeplay::getUserBranchStats($playid,$branchid,$poinsystem);

        if($branchstats['completed_actions_with_points'] == 0 OR $branchstats['totalactions_with_points'] == 0){
            $progress = 0;
        } else {
            $progress = $branchstats['completed_actions_with_points'] / $branchstats['totalactions_with_points'];
        }

        if($branchstats['points'] == 0 OR $branchstats['max_possible_points_so_far'] == 0){
            $provess = 0;
        } else {
            $provess = $branchstats['points'] / $branchstats['max_possible_points_so_far'];
        }

        $branchstats['provess'] = $provess;
        $branchstats['progress'] = $progress;

        return $branchstats;

    }


}