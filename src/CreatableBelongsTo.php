<?php

namespace Sparclex\NovaCreatableBelongsTo;

use App\Models\Study;
use Illuminate\Validation\Rules\Unique;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Field;
use Laravel\Nova\Fields\ResourceRelationshipGuesser;
use Laravel\Nova\Http\Requests\NovaRequest;

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
     * Attribute for the name of the related resource
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
            $this->belongsToId = $value->getKey();

            $this->value = $this->formatDisplayValue($value);
        }
    }

    public function prepopulate($query = null)
    {
        $this->meta['prepopulate'] = true;
        $this->meta['prepopulate_query'] = $query;
        return $this;
    }

    public function checkForCreationUsing(callable $creationCheckCallback = null) {
        $this->creationCheckCallback = $creationCheckCallback;
    }

    public function shouldCreateResource(NovaRequest $request)
    {
        if($this->creationCheckCallback) {
            return call_user_func($this->creationCheckCallback, $request);
        }

        return is_string($request->get($this->attribute));
    }

    public function getRules(NovaRequest $request)
    {
        if (!$this->shouldCreateResource($request)) {
            return parent::getRules($request);
        }
        return array_merge_recursive(Field::getRules($request), [
            $this->attribute => array_filter([
                $this->nullable ? 'nullable' : 'required',
                new Unique($this->relatedModelTableName(), $this->nameAttribute)
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
        if (!$this->shouldCreateResource($request)) {
            parent::fill($request, $model);
            return;
        }

        $relatedModel = forward_static_call(
            [$this->resourceClass, 'newModel']
        );

        $relatedModel->{$this->nameAttribute} = $request->{$this->attribute};
        $relatedModel->save();

        $model->{$model->{$this->attribute}()->getForeignKey()} = $relatedModel->getKey();

        if ($this->filledCallback) {
            call_user_func($this->filledCallback, $request, $model);
        }
    }
}
