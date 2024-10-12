<?php

namespace App\Domain\GenerationSchedules\DTO;

class StoreGenerationScheduleDTO
{
    /**
     * @var int
     */
    private int $subject_group_id;

    /**
     * @var int
     */
    private int $room_id;

    /**
     * @var string
     */
    private string $date;

    /**
     * @var string
     */
    private string $start_at;

    /**
     * @var string
     */
    private string $end_at;

    /**
     * @var int
     */
    private int $pair;

    /**
     * @param array $data
     * @return StoreGenerationScheduleDTO
     */
    public static function fromArray(array $data): StoreGenerationScheduleDTO
    {
        $dto = new self();
        $dto->setSubjectGroupId($data['subject_group_id']);
        $dto->setRoomId($data['room_id']);
        $dto->setDate($data['date']);
        $dto->setStartAt($data['start_at']);
        $dto->setEndAt($data['end_at']);
        $dto->setPair($data['pair']);

        return $dto;
    }

    /**
     * @return int
     */
    public function getSubjectGroupId(): int
    {
        return $this->subject_group_id;
    }

    /**
     * @param int $subject_group_id
     */
    public function setSubjectGroupId(int $subject_group_id): void
    {
        $this->subject_group_id = $subject_group_id;
    }

    /**
     * @return int
     */
    public function getRoomId(): int
    {
        return $this->room_id;
    }

    /**
     * @param int $room_id
     */
    public function setRoomId(int $room_id): void
    {
        $this->room_id = $room_id;
    }

    /**
     * @return string
     */
    public function getDate(): string
    {
        return $this->date;
    }

    /**
     * @param string $date
     */
    public function setDate(string $date): void
    {
        $this->date = $date;
    }

    /**
     * @return string
     */
    public function getStartAt(): string
    {
        return $this->start_at;
    }

    /**
     * @param string $start_at
     */
    public function setStartAt(string $start_at): void
    {
        $this->start_at = $start_at;
    }

    /**
     * @return string
     */
    public function getEndAt(): string
    {
        return $this->end_at;
    }

    /**
     * @param string $end_at
     */
    public function setEndAt(string $end_at): void
    {
        $this->end_at = $end_at;
    }

    /**
     * @return int
     */
    public function getPair(): int
    {
        return $this->pair;
    }

    /**
     * @param int $pair
     */
    public function setPair(int $pair): void
    {
        $this->pair = $pair;
    }
}
