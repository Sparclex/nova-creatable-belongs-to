<?php

namespace Sparclex\NovaCreatableBelongsTo;

use Laravel\Nova\Fields\Field;
use Laravel\Nova\TrashedStatus;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Fields\ResourceRelationshipGuesser;

class CreatableBelongsTo extends BelongsTo
{
    public $searchable = true;
    /**
     * The field's component.
     *
     * @var string
     */
    public $component = 'nova-creatable-belongs-to';
    /**
     * Attribute for the name of the related resource.
     *
     * @var string
     */
    public $nameAttribute;

    /**
     * @var callable
     */
    public $creationCheckCallback;

    public function __construct($name, $attribute = null, $resource = null, $nameAttribute = 'name')
    {
        $resource = $resource ?? ResourceRelationshipGuesser::guessResource($name);
        parent::__construct($name, $attribute, $resource);

        $this->nameAttribute = $nameAttribute;
    }

    /**
     * Build an associatable query for the field.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest $request
     * @param  bool $withTrashed
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function buildAssociatableQuery(NovaRequest $request, $withTrashed = false)
    {
        $model = forward_static_call(
            [$resourceClass = $this->resourceClass, 'newModel']
        );

        $query = $request->first === 'true'
            ? $model->newQueryWithoutScopes()->where($this->nameAttribute, $request->current)
            : $resourceClass::buildIndexQuery(
                $request, $model->newQuery(), $request->search,
                [], [], TrashedStatus::fromBoolean($withTrashed)
            );

        return $query->tap(function ($query) use ($request, $model) {
            forward_static_call($this->associatableQueryCallable($request, $model), $request, $query);
        });
    }

    /**
     * Resolve the field's value.
     *
     * @param  mixed $resource
     * @param  string|null $attribute
     * @return void
     */
    public function resolve($resource, $attribute = null)
    {
        $value = $resource->{$this->attribute};

        if ($value) {
            $this->belongsToId = $this->formatDisplayValue($value);

            $this->value = $this->formatDisplayValue($value);
        }
    }

    public function prepopulate($query = null)
    {
        $this->meta['prepopulate'] = true;
        $this->meta['prepopulate_query'] = $query;

        return $this;
    }

    public function checkForCreationUsing(callable $creationCheckCallback = null)
    {
        $this->creationCheckCallback = $creationCheckCallback;
    }

    public function getRules(NovaRequest $request)
    {
        return array_merge_recursive(Field::getRules($request), [
            $this->attribute => array_filter([
                $this->nullable ? 'nullable' : 'required',
            ]),
        ]);
    }

    protected function relatedModelTableName()
    {
        $relatedModel = forward_static_call(
            [$this->resourceClass, 'newModel']
        );

        return $relatedModel->getTable();
    }

    public function fill(NovaRequest $request, $model)
    {
        $relatedModel = forward_static_call(
            [$this->resourceClass, 'newModel']
        );

        $relatedModel = $relatedModel::firstOrCreate([
            $this->nameAttribute => $request->{$this->attribute},
        ]);

        $model->{$model->{$this->attribute}()->getForeignKey()} = $relatedModel->getKey();

        if ($this->filledCallback) {
            call_user_func($this->filledCallback, $request, $model);
        }
    }

    /**
     * Format the given associatable resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest $request
     * @param  mixed $resource
     * @return array
     */
    public function formatAssociatableResource(NovaRequest $request, $resource)
    {
        return array_filter([
            'avatar' => $resource->resolveAvatarUrl($request),
            'display' => $this->formatDisplayValue($resource),
            'value' => $this->formatDisplayValue($resource),
        ]);
    }
}
