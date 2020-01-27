<?php namespace spec\Monolith\WebSessions;

use Monolith\ComponentBootstrapping\ComponentLoader;
use Monolith\Configuration\ConfigurationBootstrap;
use Monolith\DependencyInjection\Container;
use Monolith\Http\Request;
use Monolith\Http\Response;
use Monolith\WebSessions\SessionData;
use Monolith\WebSessions\WebSessions;
use Monolith\WebSessions\WebSessionsBootstrap;
use Monolith\WebSessions\WebSessionStorage;
use PhpSpec\ObjectBehavior;

class WebSessionsSpec extends ObjectBehavior
{
    /** @var Container */
    private $container;
    /** @var InMemoryWebSessionsStorage */
    private $storage;
    /** @var SessionData */
    private $sessionData;

    function bootstrapMonolith(): Container
    {
        $container = new Container;
        $loader = new ComponentLoader($container);
        $loader->register(
            new ConfigurationBootstrap('spec/.env'),
            new WebSessionsBootstrap
        );
        $loader->load();
        return $container;
    }

    function let()
    {
        $this->container = $this->bootstrapMonolith();

        // clear redis cache
        $this->container->get(\Predis\Client::class)->flushDb();

        // instantiate
        $this->storage = $this->container->get(WebSessionStorage::class);
        $this->sessionData = $this->container->get(SessionData::class);

        $this->beConstructedWith($this->storage, $this->sessionData);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(WebSessions::class);
    }

    function it_can_store_session_data()
    {
        $_COOKIE['session_id'] = '123';

        $request = Request::fromGlobals();

        $this->process($request, function (Request $r) {
            /** @var SessionData $sessionData */
            $sessionData = $this->container->get(SessionData::class);

            expect($sessionData)->has('example')->shouldBe(false);
            $sessionData->set('example', '321');

            return Response::ok('');
        });

        $data = expect($this->storage->retrieve('session_data_123'));

        $data->shouldHaveType(SessionData::class);
        $data->has('example')->shouldBe(true);
        $data->get('example')->shouldBe('321');
    }

    function it_can_retrieve_session_data_from_a_request()
    {
        $_COOKIE['session_id'] = '234';

        $this->storage->store('session_data_234', SessionData::fromArray(['example' => 'abc']));

        $request = Request::fromGlobals();

        $this->process($request, function (Request $r) {
            /** @var SessionData $sessionData */
            $sessionData = $this->container->get(SessionData::class);

            expect($sessionData->get('example'))->shouldBe('abc');

            return Response::ok('');
        });
    }

    function it_can_remove_session_data()
    {
        $_COOKIE['session_id'] = '234';

        $this->storage->store('session_data_234', SessionData::fromArray(['example' => 'abc']));

        $request = Request::fromGlobals();

        $this->process($request, function (Request $r) {
            /** @var SessionData $sessionData */
            $sessionData = $this->container->get(SessionData::class);

            expect($sessionData->has('example'))->shouldBe(true);
            $sessionData->remove('example');

            return Response::ok('');
        });

        $data = expect($this->storage->retrieve('session_data_234'));
        $data->has('example')->shouldBe(false);
    }
}
