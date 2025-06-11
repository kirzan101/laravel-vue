<?php

namespace Tests\Helpers;

use App\Interfaces\BaseInterface;
use App\Interfaces\FetchInterfaces\BaseFetchInterface;
use App\Interfaces\AuthInterface;
use Mockery;

trait TestDoubles
{
    /**
     * create a mock of BaseInterface.
     *
     * @return BaseInterface
     */
    protected function mockBaseInterface(): BaseInterface
    {
        return Mockery::mock(BaseInterface::class);
    }

    /**
     * create a mock of BaseFetchInterface.
     *
     * @return BaseFetchInterface
     */
    protected function mockBaseFetchInterface(): BaseFetchInterface
    {
        return Mockery::mock(BaseFetchInterface::class);
    }

    /**
     * create a mock of AuthInterface.
     *
     * @return AuthInterface
     */
    protected function mockAuthInterface(): AuthInterface
    {
        return Mockery::mock(AuthInterface::class);
    }
}
