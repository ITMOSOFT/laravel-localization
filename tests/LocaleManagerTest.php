<?php

namespace ItmoSoft\Localization\Tests;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use InvalidArgumentException;
use ItmoSoft\Localization\LocaleManager;
use ItmoSoft\Localization\Models\LocalePreference;
use PHPUnit\Framework\Attributes\Test;

class LocaleTestSubject extends Model
{
    protected $table = 'locale_test_subjects';
    protected $guarded = [];
    public $timestamps = false;
}

class LocaleManagerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('locale_test_subjects', function ($table): void {
            $table->id();
        });
    }

    private function manager(): LocaleManager
    {
        return $this->app->make(LocaleManager::class);
    }

    #[Test]
    public function it_returns_configured_supported_locales(): void
    {
        $this->assertSame(['en', 'ur', 'ar', 'en-GB'], $this->manager()->supported());
        $this->assertTrue($this->manager()->isSupported('ur'));
        $this->assertFalse($this->manager()->isSupported('fr'));
    }

    #[Test]
    public function it_returns_the_configured_fallback(): void
    {
        $this->assertSame('en', $this->manager()->fallback());
    }

    #[Test]
    public function it_rejects_an_unsupported_fallback(): void
    {
        config(['localization.fallback_locale' => 'fr']);
        $this->expectException(InvalidArgumentException::class);
        $this->manager()->fallback();
    }

    #[Test]
    public function it_persists_and_reads_a_subject_preference(): void
    {
        $subject = LocaleTestSubject::query()->create();
        $this->manager()->setPreference($subject, 'ur');

        $this->assertSame('ur', $this->manager()->preferenceFor($subject));
        $this->assertDatabaseCount('locale_preferences', 1);
    }

    #[Test]
    public function it_rejects_unsupported_subject_preferences(): void
    {
        $subject = LocaleTestSubject::query()->create();
        $this->expectException(InvalidArgumentException::class);
        $this->manager()->setPreference($subject, 'fr');
    }

    #[Test]
    public function it_updates_existing_preferences_and_can_forget_them(): void
    {
        $subject = LocaleTestSubject::query()->create();
        $this->manager()->setPreference($subject, 'ur');
        $this->manager()->setPreference($subject, 'ar');

        $this->assertSame('ar', $this->manager()->preferenceFor($subject));
        $this->manager()->forgetPreference($subject);
        $this->assertNull($this->manager()->preferenceFor($subject));
    }

    #[Test]
    public function route_locale_has_highest_resolution_priority(): void
    {
        $request = Request::create('/ur/profile');
        $request->setRouteResolver(fn () => new class {
            public function parameter(string $key): ?string
            {
                return $key === 'locale' ? 'ur' : null;
            }
        });
        $request->headers->set('Accept-Language', 'ar');

        $this->assertSame('ur', $this->manager()->resolve($request));
    }

    #[Test]
    public function accept_language_supports_exact_and_base_locale_matches(): void
    {
        $exact = Request::create('/');
        $exact->headers->set('Accept-Language', 'ar,en;q=0.8');
        $this->assertSame('ar', $this->manager()->resolve($exact));

        $base = Request::create('/');
        $base->headers->set('Accept-Language', 'en-AU');
        $this->assertSame('en', $this->manager()->resolve($base));
    }

    #[Test]
    public function it_falls_back_when_no_supported_locale_is_requested(): void
    {
        $request = Request::create('/');
        $request->headers->set('Accept-Language', 'fr,de');

        $this->assertSame('en', $this->manager()->resolve($request));
    }

    #[Test]
    public function it_sets_the_application_locale_and_rejects_unknown_values(): void
    {
        $this->manager()->set('ur');
        $this->assertSame('ur', app()->getLocale());

        $this->expectException(InvalidArgumentException::class);
        $this->manager()->set('fr');
    }
}
