<?php

/*
 * This file is part of the hyn/multi-tenant package.
 *
 * (c) Daniël Klabbers <daniel@klabbers.email>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @see https://laravel-tenancy.com
 * @see https://github.com/hyn/multi-tenant
 */

namespace Hyn\Tenancy\Tests;

use Hyn\Tenancy\Contracts\CurrentHostname;
use Hyn\Tenancy\Environment;
use Hyn\Tenancy\Events\Hostnames\Identified;
use Hyn\Tenancy\Events\Hostnames\Switched;
use Hyn\Tenancy\Jobs\HostnameIdentification;
use Illuminate\Contracts\Foundation\Application;

class EnvironmentTest extends Test
{
    /**
     * @var Environment
     */
    protected $environment;

    /**
     * @test
     */
    public function sets_hostname()
    {
        $this->expectsEvents(Switched::class);

        $this->environment->hostname($this->hostname);

        $identified = $this->app->make(CurrentHostname::class);

        $this->assertEquals($this->hostname->fqdn, $identified->fqdn);
    }

    /**
     * @test
     */
    public function identifies_hostname()
    {
        $this->expectsEvents(Identified::class);

        $identified = $this->app->make(CurrentHostname::class);

        $this->assertNull($identified);

        $this->hostname->save();
        $this->app->make('config')->set('tenancy.hostname.default', $this->hostname->fqdn);

        $identified = $this->app->make(CurrentHostname::class);

        $this->assertEquals($this->hostname->fqdn, $identified->fqdn);

        $this->assertEquals($identified->fqdn, $this->environment->hostname()->fqdn);
    }


    protected function duringSetUp(Application $app)
    {
        $this->setUpHostnames();
        $this->environment = $app->make(Environment::class);
    }
}
