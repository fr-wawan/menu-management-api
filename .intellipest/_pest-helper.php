<?php

namespace {

    /**
     * Runs the given closure after each test in the current file.
     *
     * @param-closure-this \Tests\TestCase  $closure
     *
     * @return \Pest\Concerns\Expectable|\Pest\Support\HigherOrderTapProxy<\Pest\Concerns\Expectable|\Pest\PendingCalls\TestCall|\Tests\TestCase>|\Pest\PendingCalls\TestCall|mixed
     */
    function afterEach(?Closure $closure = null): \Pest\PendingCalls\AfterEachCall {}

    /**
     * Runs the given closure before each test in the current file.
     *
     * @param-closure-this \Tests\TestCase  $closure
     *
     * @return \Pest\Support\HigherOrderTapProxy<\Pest\Concerns\Expectable|\Pest\PendingCalls\TestCall|\Tests\TestCase|mixed
     */
    function beforeEach(?Closure $closure = null): \Pest\PendingCalls\BeforeEachCall {}

    /**
     * Adds the given closure as a test. The first argument
     * is the test description; the second argument is
     * a closure that contains the test expectations.
     *
     * @param-closure-this \Tests\TestCase  $closure
     *
     * @return \Pest\Concerns\Expectable|\Pest\PendingCalls\TestCall|\Tests\TestCase|mixed
     */
    function test(?string $description = null, ?Closure $closure = null): \Pest\Support\HigherOrderTapProxy|\Pest\PendingCalls\TestCall {}

    /**
     * Adds the given closure as a test. The first argument
     * is the test description; the second argument is
     * a closure that contains the test expectations.
     *
     * @param-closure-this \Tests\TestCase  $closure
     *
     * @return \Pest\Concerns\Expectable|\Pest\PendingCalls\TestCall|\Tests\TestCase|mixed
     */
    function it(string $description, ?Closure $closure = null): \Pest\PendingCalls\TestCall {}

}

namespace Pest {

    /**
     * @method self toBeOne()
     */
    class Expectation {}

}

namespace Pest\Expectations {

    /**
     * @method self toBeOne()
     */
    class OppositeExpectation {}

}

namespace Tests {

    class TestCase
    {
        use \Illuminate\Foundation\Testing\RefreshDatabase;
    }

}
