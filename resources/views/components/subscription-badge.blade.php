@props(['entity', 'placement' => 'top', 'title' => 'دارای اشتراک'])
@if($entity->isSubscriber())
    <i class="icofont icofont-star txt-warning cursor-pointer"
       data-bs-toggle="tooltip"
       data-bs-placement="{{ $placement }}"
       title="{{ $title }}"
       data-entity-id="{{ $entity->wallet->id }}"
       wire:ignore
    ></i>
@endif

@pushonce('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const starIcons = document.querySelectorAll('.icofont-star');

            starIcons.forEach(icon => {
                icon.addEventListener('click', e => {
                    e.preventDefault();

                    const entityId = e.currentTarget.dataset.entityId;

                    if (entityId) {
                        const route = '{{ route('profile.subscription.show', 'ENTITY_ID_PLACEHOLDER') }}';
                        const finalRoute = route.replace('ENTITY_ID_PLACEHOLDER', entityId);
                        window.open(finalRoute, '_blank');
                    }
                });
            });
        });
    </script>
@endpushonce
