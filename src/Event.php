<?php
declare(strict_types=1);

namespace Fyre\Event;

use RuntimeException;

/**
 * Event
 */
class Event
{
    protected bool $cancelable = true;

    protected array $data;

    protected bool $defaultPrevented = false;

    protected string $name;

    protected bool $propagationStopped = false;

    protected mixed $result = null;

    protected bool $stopped = false;

    protected object|null $subject;

    /**
     * New Event constructor.
     *
     * @param string $name The Event name.
     * @param object|null $subject The Event subject.
     * @param array $data The Event data.
     * @param bool $cancelable Whether the Event is cancelable.
     */
    public function __construct(string $name, object|null $subject = null, array $data = [], bool $cancelable = true)
    {
        $this->name = $name;
        $this->subject = $subject;
        $this->data = $data;
        $this->cancelable = $cancelable;
    }

    /**
     * Get the Event data.
     *
     * @return array The Event data.
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * Get the Event name.
     *
     * @return array The Event name.
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get the Event result.
     *
     * @return mixed The Event result.
     */
    public function getResult(): mixed
    {
        return $this->result;
    }

    /**
     * Get the Event subject.
     *
     * @return object|null The Event subject.
     */
    public function getSubject(): object|null
    {
        return $this->subject;
    }

    /**
     * Determine whether the default Event should occur.
     *
     * @return bool TRUE if the default Event should not occur, otherwise FALSE.
     */
    public function isDefaultPrevented(): bool
    {
        return $this->defaultPrevented;
    }

    /**
     * Determine whether the Event propagation was stopped.
     *
     * @return bool TRUE if the Event propagation was stopped, otherwise FALSE.
     */
    public function isPropagationStopped(): bool
    {
        return $this->propagationStopped;
    }

    /**
     * Determine whether the Event was stopped.
     *
     * @return bool TRUE if the Event was stopped, otherwise FALSE.
     */
    public function isStopped(): bool
    {
        return $this->stopped;
    }

    /**
     * Prevent the default Event.
     *
     * @return Event The Event.
     *
     * @throws RuntimeException if the event can not be cancelled.
     */
    public function preventDefault(): static
    {
        if (!$this->cancelable) {
            throw new RuntimeException('Event can not be cancelled: '.$this->name);
        }

        $this->defaultPrevented = true;

        return $this;
    }

    /**
     * Set the Event data.
     *
     * @param array $data The Event data.
     * @return Event The Event.
     */
    public function setData(array $data): static
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Set the Event result.
     *
     * @param mixed $result The Event result.
     * @return Event The Event.
     */
    public function setResult(mixed $result): static
    {
        $this->result = $result;

        return $this;
    }

    /**
     * Stop the Event propagating immediately.
     *
     * @return Event The Event.
     */
    public function stopImmediatePropagation(): static
    {
        $this->stopped = true;

        return $this->stopPropagation();
    }

    /**
     * Stop the Event propagating.
     *
     * @return Event The Event.
     */
    public function stopPropagation(): static
    {
        $this->propagationStopped = true;

        return $this;
    }
}
