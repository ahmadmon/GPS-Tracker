<?php

namespace App\Http\Controllers\Subscription;

use App\Facades\Acl;
use App\Http\Controllers\Controller;
use App\Http\Requests\SubscriptionPlanRequest;
use App\Models\SubscriptionPlan;
use Illuminate\Support\Facades\Cache;

class SubscriptionPlanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        Acl::authorize('plan-list');

        $plans = Cache::remember('subscription-plan', 60, static fn() => SubscriptionPlan::latest()->get());

        return view('subscription-plan.index', compact('plans'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        Acl::authorize('create-plan');


        return view('subscription-plan.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(SubscriptionPlanRequest $request)
    {
        Acl::authorize('create-plan');

        $inputs = $request->validated();

        SubscriptionPlan::create($inputs);

        return to_route('subscription-plan.index')->with('success-alert', 'طرح اشتراک جدید با موفقیت تعریف شد.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        abort(404);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $slug)
    {
        Acl::authorize('edit-plan');

        $plan = $this->findPlan($slug);

        return view('subscription-plan.edit', compact('plan'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(SubscriptionPlanRequest $request, string $slug)
    {
        Acl::authorize('edit-plan');

        $inputs = $request->validated();
        $plan = $this->findPlan($slug);

        $plan->update($inputs);

        return to_route('subscription-plan.index')->with('success-alert', 'طرح اشتراک با موفقیت ویرایش شد.');

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $slug)
    {
        Acl::authorize('delete-plan');

        $plan = $this->findPlan($slug);
        $plan->delete();

        return to_route('subscription-plan.index')->with('success-alert', 'طرح اشتراک با موفقیت حذف گردید.');

    }

    public function changeStatus(string $slug)
    {
        Acl::authorize('edit-plan');

        $plan = $this->findPlan($slug);

        $plan->status = $plan->status == 0 ? 1 : 0;
        $plan->save();

        return response()->json(['status' => true, 'data' => (bool)$plan->status]);
    }

    private function findPlan(string $slug)
    {
        return SubscriptionPlan::where('slug', $slug)->firstOrFail();
    }


}
