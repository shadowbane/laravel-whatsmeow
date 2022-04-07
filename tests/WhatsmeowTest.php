<?php

uses(\Shadowbane\Whatsmeow\Tests\TestCase::class);
uses()
    ->beforeEach(function () {
        \Illuminate\Support\Facades\Http::flushMacros();
        config(['whatsmeow.endpoint' => 'http://localhost/api/v2']);
    });

it('can be instantiated', function () {
    $this->assertInstanceOf(\Shadowbane\Whatsmeow\Whatsmeow::class, new \Shadowbane\Whatsmeow\Whatsmeow());
});

it('has custom macro after initialized', function () {
    new \Shadowbane\Whatsmeow\Whatsmeow();

    $this->assertTrue(\Illuminate\Support\Facades\Http::hasMacro('whatsmeow'));
});

it('can set and get endpoint', function () {
    $whatsmeow = new \Shadowbane\Whatsmeow\Whatsmeow();

    $whatsmeow->setEndpoint('testing/');

    $this->assertEquals('/testing', $whatsmeow->getEndpoint());
});

it('can set and get token', function () {
    $whatsmeow = new \Shadowbane\Whatsmeow\Whatsmeow();

    $whatsmeow->setToken('testing');

    $this->assertEquals('testing', $whatsmeow->getToken());
});

it('throws error when endpoint is not set', function () {
    config([
        'whatsmeow.endpoint' => '',
    ]);

    $this->expectErrorMessage('Whatsmeow API Endpoint url is empty');
    $this->expectException(\Shadowbane\Whatsmeow\Exceptions\WhatsmeowException::class);

    new \Shadowbane\Whatsmeow\Whatsmeow();
});

it('throws error when server returned with 500 when sending message', function () {
    // faking response
    \Illuminate\Support\Facades\Http::fake([
        'http://localhost/*' => \Illuminate\Support\Facades\Http::response(status: 500),
    ]);


    $this->expectException(\Shadowbane\Whatsmeow\Exceptions\WhatsmeowException::class);

    $whatsmeow = new \Shadowbane\Whatsmeow\Whatsmeow();
    $whatsmeow->sendMessage([]);
});

it('throws error when server returned with 404 when sending message', function () {
    // faking response
    \Illuminate\Support\Facades\Http::fake([
        'http://localhost/*' => \Illuminate\Support\Facades\Http::response(body: ['message' => 'What do you looking at?'], status: 404),
    ]);

    $this->expectException(\Shadowbane\Whatsmeow\Exceptions\WhatsmeowException::class);

    $whatsmeow = new \Shadowbane\Whatsmeow\Whatsmeow();
    $whatsmeow->sendMessage([]);
});

it('can send message with bearer token if set', function () {
    // faking response
    \Illuminate\Support\Facades\Http::fake([
        'http://localhost/*' => \Illuminate\Support\Facades\Http::response(body: ['foo' => 'bar'], status: 200),
    ]);

    $whatsmeow = new \Shadowbane\Whatsmeow\Whatsmeow();
    $whatsmeow->setToken('testing')->sendMessage([]);

    \Illuminate\Support\Facades\Http::assertSent(function (\Illuminate\Http\Client\Request $request) {
        return $request->hasHeader('Authorization', 'Bearer testing');
    });
});

it('can be set to debug mode', function () {
    new \Shadowbane\Whatsmeow\Whatsmeow();
    $this->assertTrue(\Illuminate\Support\Facades\Http::whatsmeow()->getOptions()['debug']);
});

it('returns array', function () {
    // faking response
    \Illuminate\Support\Facades\Http::fake([
        'http://localhost/*' => \Illuminate\Support\Facades\Http::response(body: ['foo' => 'bar'], status: 200),
    ]);

    $whatsmeow = new \Shadowbane\Whatsmeow\Whatsmeow();
    $result = $whatsmeow->sendMessage([]);

    $this->assertIsArray($result);
    $this->assertArrayHasKey('foo', $result);
});
