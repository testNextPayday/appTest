<?php
namespace App\Services;

use App\Models\GroupNotification;


class GroupNotificationService
{
    
    /**
     * Creates a notification belonging to a particular group
     *
     * @param  \Illuminate\Database\Eloquent\Model $entity
     * @param  string $type
     * @return void
     */
    public function createNotification($entity, $type)
    {

        $data = $this->getTypeDetails($type);
        
        return GroupNotification::create(
            [
                'entity_id'=> $entity->id,
                'entity_type'=> get_class($entity),
                'permission_level'=> json_encode($data['permissions']),
                'type'=> $type
            ]
        );
    }
    
    /**
     * Get the details of a particular type
     *
     * @param  mixed $type
     * @return void
     */
    protected function getTypeDetails($type)
    {
        return config('settings.notification_code')[$type];
    }

    
    /**
     * Marks a particular notification as handled
     *
     * @param  \Illuminate\Database\Eloquent\Model $model
     * @param  integer $notificationId
     * @return void
     */
    public function handleNotification($model, $notificationId)
    {
        if (! $notificationId) {

            return false;
        }
        $notification = GroupNotification::find($notificationId);

        $notification->update(
            [
                'read'=> true,
                'responder_id'=> $model->id,
                'responder_type'=> get_class($model)
            ]
        );

        return true;
    }
}