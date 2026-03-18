<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Events\ApprovalSaved;

class Approval extends Model
{
	protected $fillable = ['approved_by_role', 'approved_by', 'approval_status', 'comments', 'approval_statuses'];

	protected $casts = [
		'approval_statuses' => 'json',
	];

    protected $dispatchesEvents = [
        'saved' => ApprovalSaved::class,
    ];

	public function approvable(){
		return $this->morphTo();
	}

	public function approver(){
		return $this->belongsTo('App\User', 'approved_by', 'id');
	}

	public function approver_role(){
		return $this->belongsTo('App\Role', 'approved_by_role', 'id');
	}

	/**
	 * Initialize approval statuses for all roles in the approval workflow chain
	 * 
	 * @param array $role_ids Array of role IDs from the approval workflow
	 * @return void
	 */
	public function initializeApprovalStatuses($role_ids = [])
	{
		if (empty($role_ids)) return;

		$statuses = [];
		foreach ($role_ids as $role_id) {
			$statuses[$role_id] = [
				'status' => null, // null = pending, 1 = approved, 0 = rejected
				'approved_at' => null,
				'approved_by_user_id' => null,
			];
		}

		$this->approval_statuses = $statuses;
	}

	/**
	 * Update a specific role's approval status in the approval_statuses array
	 * 
	 * @param int $role_id Role ID to update
	 * @param int $status Status value (null/0/1)
	 * @param int $user_id User ID who approved/rejected
	 * @return void
	 */
	public function updateApprovalStatus($role_id, $status, $user_id = null)
	{
		$statuses = $this->approval_statuses ?? [];

		if (!isset($statuses[$role_id])) {
			$statuses[$role_id] = [];
		}

		$statuses[$role_id] = [
			'status' => $status,
			'approved_at' => $status !== null ? now()->toDateTimeString() : null,
			'approved_by_user_id' => $user_id,
		];

		$this->approval_statuses = $statuses;
	}

	/**
	 * Get approval status for a specific role
	 * 
	 * @param int $role_id Role ID to check
	 * @return array|null
	 */
	public function getApprovalStatusForRole($role_id)
	{
		$statuses = $this->approval_statuses ?? [];
		return $statuses[$role_id] ?? null;
	}
}
