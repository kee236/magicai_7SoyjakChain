<div class="row mt-4" id="team-members">
    <div class="col-md-12">
        <div class="card m-0 p-0">
            <div class="card-body">
                <p class="font-bold fs-3">@lang('Team Members')</p>
            </div>
            <div id="table-default" class="card-table table-responsive">
                <table class="table">
                    <thead>
                    <tr>
                        <th>
                            @lang('Name')
                        </th>
                        <th>
                            @lang('Status')
                        </th>
                        <th>
                            @lang('Joined')
                        </th>
                        <th>
                            @lang('Role')
                        </th>
                        <th>
                            @lang('Images / Words')
                        </th>
                        <th class="!text-center">Actions</th>
                    </tr>
                    </thead>
                    <tbody class="table-tbody align-middle text-heading">
                    @foreach($members as $member)
                        <tr>
                            <td>
                                @if($member->user_id)
                                    <p class="m-0">{{ $member?->user?->name . ' ' . $member?->user?->surname }}</p>
                                @endif
                                <p class="m-0 text-muted">{{ $member->email }}</p>
                            </td>
                            <td>
                            <span
                                    class="badge bg- bg-{{ $member->status == 'waiting' ? 'gray-500 text-dark' : ($member->status == 'active' ? 'success' : 'danger') }}"
                            >@lang($member->status)</span>
                            </td>
                            <td>
                                @if($member->joined_at)
                                    <p class="m-0">{{ $member->joined_at->format('M d, Y') }}</p>
                                    <p class="m-0 text-muted">{{ $member->joined_at->format('H:i:s') }}</p>
                                @endif
                            </td>
                            <td>{{ $member->role ?: __('unknown') }}</td>
                            <td>
                                <p class="m-0">
                                    @if($member->remaining_images)
                                        {{ $member->remaining_images }}
                                    @else
                                        {{ $user->remaining_images }}
                                    @endif
                                    /
                                    @if($member->remaining_words)
                                            {{ $member->remaining_words }}
                                        @else
                                        {{ $user->remaining_words }}
                                        @endif
                                </p>
                            </td>
                            <td class=" whitespace-nowrap">
                                <a href="{{ route('dashboard.user.team.member.edit', [$team->id, $member->id]) }}"
                                   class="btn w-[36px] h-[36px] p-0 border hover:bg-[var(--tblr-primary)] hover:text-white"
                                   title="Edit">
                                    <svg width="13" height="12" viewBox="0 0 15 14" fill="none"
                                         stroke="currentColor" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M8.71875 2.43988L11.9688 5.58995M10.75 11.4963H14M4.25 13.0714L12.7812 4.80248C12.9946 4.59564 13.1639 4.35009 13.2794 4.07984C13.3949 3.8096 13.4543 3.51995 13.4543 3.22744C13.4543 2.93493 13.3949 2.64528 13.2794 2.37504C13.1639 2.10479 12.9946 1.85924 12.7812 1.6524C12.5679 1.44557 12.3145 1.28149 12.0357 1.16955C11.7569 1.05761 11.458 1 11.1562 1C10.8545 1 10.5556 1.05761 10.2768 1.16955C9.99799 1.28149 9.74465 1.44557 9.53125 1.6524L1 9.92135V13.0714H4.25Z"
                                              stroke-width="1.25" stroke-linecap="round"
                                              stroke-linejoin="round"></path>
                                    </svg>
                                </a>
                                <a
                                   href="{{ route('dashboard.user.team.member.delete', [$team->id, $member->id]) }}"
                                   onclick="return confirm('Are you sure? This is permanent and will delete all documents related to user.')"
                                   class="btn w-[36px] h-[36px] p-0 border hover:bg-red-500 hover:text-white"
                                   title="Delete">
                                    <svg width="10" height="10" viewBox="0 0 10 10" fill="currentColor"
                                         xmlns="http://www.w3.org/2000/svg">
                                        <path d="M9.08789 1.74609L5.80664 5L9.08789 8.25391L8.26758 9.07422L4.98633 5.82031L1.73242 9.07422L0.912109 8.25391L4.16602 5L0.912109 1.74609L1.73242 0.925781L4.98633 4.17969L8.26758 0.925781L9.08789 1.74609Z"></path>
                                    </svg>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                    <tfoot>

                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>