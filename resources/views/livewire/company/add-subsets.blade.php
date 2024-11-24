<div class="card height-equal">
    <div class="card-header card-no-border">
        <div class="header-top">
            <h5>لیست کاربران</h5>
            <div>
                <div class="input-group product-search">
                        <span class="input-group-text">
                            <svg
                                xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="feather feather-search search-icon text-gray"><circle
                                    cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65"
                                                                         y2="16.65"></line></svg>
                        </span>
                    <input class="form-control" type="text" placeholder="جستجو..."
                           id="search" aria-label="search..." wire:model.live.debounce.850ms="search">
                </div>
            </div>
        </div>
    </div>
    <div class="card-body pt-0 task-table">
        <div class="recent-table table-responsive currency-table task-table">
            <table class="table">
                <tbody class="main-task-wrapper">
                @forelse($users as $key => $user)
                    <tr @class(['card-light' => $loop->even]) wire:key="{{ $user->id }}">
                        <td>
                            <div class="d-flex">
                                <div class="form-check checkbox-width checkbox checkbox-primary mb-0">
                                    <input class="from-check-input"
                                           value="{{ $user->id }}"
                                           id="checkbox-{{ $key }}"
                                           wire:loading.class="d-none"
                                           wire:target="handleSelection({{ (int)$user->id }})"
                                           wire:change="handleSelection({{ (int)$user->id }})"
                                           type="checkbox">
                                    <label class="form-check-label" for="checkbox-{{ $key }}"
                                           wire:loading.class="d-none"
                                           wire:target="handleSelection({{ (int)$user->id }})"></label>
                                    <x-partials.loaders.livewire.svg-spinner
                                        target="handleSelection({{ (int)$user->id }})" class="mt-2"/>
                                </div>
                                <div class="d-flex align-items-center gap-2 justify-content-center">
                                    <div>
                                        <h6 class="pb-1">
                                            @role(['manager'])
                                            {{ $user->name }}
                                            @else
                                             <a href="{{ route('user.show', $user->id) }}" target="_blank" class="text-dark cursor-pointer">{{ $user->name }}</a>
                                            @endrole
                                        </h6>
                                        <ul class="task-icons">
                                            <li><span class="text-muted"
                                                      dir="ltr">{{ auth()->user()->hasRole(['manager']) ? maskPhoneNumber($user->phone) : $user->phone }}</span>
                                            </li>
                                            <li class="f-light flex-wrap" wire:ignore>
                                                <svg xmlns="http://www.w3.org/2000/svg" width="17px" height="17px"
                                                     viewBox="0 0 256 256"
                                                     data-bs-toggle="tooltip"
                                                     data-bs-placement="top"
                                                     data-bs-title="تعداد دستگاه‌ها"
                                                >
                                                    <path fill="#4a4545"
                                                          d="M192 24H64a24 24 0 0 0-24 24v160a24 24 0 0 0 24 24h128a24 24 0 0 0 24-24V48a24 24 0 0 0-24-24m8 184a8 8 0 0 1-8 8H64a8 8 0 0 1-8-8V48a8 8 0 0 1 8-8h128a8 8 0 0 1 8 8ZM168 64a8 8 0 0 1-8 8H96a8 8 0 0 1 0-16h64a8 8 0 0 1 8 8"/>
                                                </svg>
                                                <span
                                                    class="me-2">{{ $user->devices_count == 0 ? 'فاقد دستگاه' : $user->devices_count }}</span>
                                                <svg xmlns="http://www.w3.org/2000/svg" width="17px" height="17px"
                                                     viewBox="0 0 24 24"
                                                     data-bs-toggle="tooltip"
                                                     data-bs-placement="top"
                                                     data-bs-title="تعداد وسایل نقلیه"
                                                >
                                                    <path fill="#4a4545"
                                                          d="M18.626 6.026A2.75 2.75 0 0 0 15.971 4h-4.83a2.75 2.75 0 0 0-2.368 1.352L6.265 9.598l-2.182.546A2.75 2.75 0 0 0 2 12.812V14.5a2.75 2.75 0 0 0 1.585 2.492a3.251 3.251 0 0 0 6.258.258h4.814a3.252 3.252 0 0 0 6.32-.61A2.75 2.75 0 0 0 22 14.5v-2.25a2.75 2.75 0 0 0-2.422-2.73zM9.962 15.75a3.25 3.25 0 0 0-6.274-.591A1.24 1.24 0 0 1 3.5 14.5v-1.688c0-.574.39-1.074.947-1.213L6.844 11H19.25c.69 0 1.25.56 1.25 1.25v2.267a3.25 3.25 0 0 0-5.962 1.233zM13 9.5H8.066l2-3.386a1.25 1.25 0 0 1 1.076-.614H13zm1.5-4h1.472a1.25 1.25 0 0 1 1.206.921l.84 3.079H14.5zm3.25 9a1.75 1.75 0 1 1 0 3.5a1.75 1.75 0 0 1 0-3.5M8.5 16.25a1.75 1.75 0 1 1-3.5 0a1.75 1.75 0 0 1 3.5 0"/>
                                                </svg>
                                                <span>{{ $user->vehicles_count == 0 ? 'فاقد وسیله‌نقلیه' : $user->vehicles_count }}</span>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td wire:ignore>
                            <span class="text-muted"
                                  data-bs-toggle="tooltip"
                                  data-bs-placement="top"
                                  data-bs-title="تاریخ عضویت"
                            >{{ jalaliDate($user->created_at) }}</span>
                        </td>
                    </tr>
                @empty
                    <tr class="text-center light-card">
                        <td colspan="2" class="f-w-900">کاربری یافت نشد...</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div class="col-12 mt-3 d-flex justify-content-center">
            {{ $users->links('livewire::custom-pagination') }}
        </div>
    </div>
</div>
