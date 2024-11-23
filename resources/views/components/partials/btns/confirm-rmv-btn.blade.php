<template x-if="show">
    <div class="btn-group btn-group-sm">
        <button class="btn btn-sm btn-dark" @click="show = false"
                data-bs-toggle="tooltip"
                data-bs-placement="top"
                data-bs-title="انصراف"
                title="انصراف"
                ><i class="fa fa-undo"></i></button>
        <form action="{{ $url }}" class="d-inline" method="post">
            @csrf @method('DELETE')
            <button type="submit" class="btn btn-sm btn-danger"
                    data-bs-toggle="tooltip"
                    data-bs-placement="top"
                    data-bs-title="حذف"
                    title="حذف"
            ><i class="fa fa-trash"></i></button>
        </form>
    </div>
</template>
