# Estudos sobre JWT
JWT (Json Web Token) É uma forma de transmitir informações entre partes de forma segura e compacta em um formato JSON.

Funciona como um cartão de identificação online, muitos sistemas usam JWTs para autenticar usuários e permitir acesso a recursos protegidos na web de maneira segura e eficiente.

O JWT é composto por três partes:

- **Header**
    Consiste em duas partes: o tipo do token (que é JWT) e o algoritmo de assinatura (HMAC SHA256 ou RSA).

- **Payload**
    O Payload é um objeto JSON com as Claims (informações) da entidade tratada, normalmente o usuário autenticado.
    
- **Signature**
    A assinatura é a concatenação dos hashes gerados a partir do Header e Payload usando base64UrlEncode, com uma chave secreta ou certificado RSA. Essa assinatura é utilizada para garantir a integridade do token.

<hr>

**Resultado:** <br>
    O resultado final é um token com três seções (header, payload, signature) separadas por “.” 

<br>

**Usando o Token:** <br>
    Ao fazer login em um serviço de autenticação um token JWT é criado e retornado para o client. Esse token deve ser enviado para as APIs através do header Authorization de cada requisição HTTP com a flag Bearer.
    Assim, a API não precisa ir até o banco de dados consultar as informações do usuário, pois contido no próprio token JWT já temos suas credenciais de acesso.

<hr>

### Implementando JWT no Laravel
**Pré-Requisitos:**
- PHP 8.3
- Composer

<br>

1. Instale a biblioteca JWT:
    ```
    composer require tymon/jwt-auth
    ```

2. Configure o pacote JWT:
    ```
    php artisan vendor:publish --provider="Tymon\JWTAuth\Providers\LaravelServiceProvider"
    ```

3. Crie o controller de Autenticação:
    ```
    php artisan make:contoller AuthController
    ```

4. Implemente a Autenticação:
    ```
        public function register(Request $request, User $user)
        {   
            $userData = $request->only('name', 'email', 'password');
            $userData['password'] = bcrypt($userData['password']);

            if (!$user = $user->create($userData)) {
                abort(500, "Error to create new user");
            }

            $token = JWTAuth::attempt(['email' => $userData['email'], 'password' => $request->password]);

            return response()->json([
            'data' => [
                'msg' => 'Successfully',
                'user' => $user,
                'token' => $token,
                ]
            ], 200);
        }

        public function login(Request $request)
        {
            $credentials = $request->only('email', 'password');

            if (!$token = auth('api')->attempt($credentials)) {
                abort(403, "Invalid Login");
            }

            return response()
                    ->json([
                    'data' => [
                            'msg' => 'Login Successful',
                            'token' => $token
                        ]
                    ]);
        }
    ```

5. Proteja suas Rotas usando o middleware auth:api:
    ```
    Route::middleware('auth:api')->prefix('auth')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::get('me', [AuthController::class, 'me']);
    });
    ```
    Assim, as rotas logout e me estão protegidas, onde, apenas usuários autenticados podem acessá-las.

<br>
<br>

**Conclusão:** <br>
    A implementação de autenticação JWT em uma API REST Laravel é uma maneira segura e eficaz de proteger seus recursos e garantir que apenas usuários autorizados tenham acesso a eles.

<hr>

### Endpoints

#### Autenticação
- Criar conta: ``` POST api/register ```

    Retorna:
    ```
            {
                "data": {
                    "msg": "Successfully",
                    "user": {
                        "name": "John Doe",
                        "email": "johndoe@email.com",
                        "updated_at": "2024-03-25T21:06:25.000000Z",
                        "created_at": "2024-03-25T21:06:25.000000Z",
                        "id": 2
                    },
                "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vbG9jYWxob3N0Ojg4ODgvYXBpL3JlZ2lzdGVyIiwiaWF0IjoxNzExNDAwNzg1LCJleHAiOjE3MTE0MDQzODUsIm5iZiI6MTcxMTQwMDc4NSwianRpIjoidzdZdlBrdlNMNnozYVBGYyIsInN1YiI6IjIiLCJwcnYiOiIyM2JkNWM4OTQ5ZjYwMGFkYjM5ZTcwMWM0MDA4NzJkYjdhNTk3NmY3In0.HYrmy1-D0SIxrVlcfbC7R_bskDtRi5yWTrClXhonyow"
            }
        }
    ```

- Login: ``` POST api/login ```
  
    Retorna:
    ```
    {
        "data": {
            "msg": "Login Successful",
            "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vbG9jYWxob3N0Ojg4ODgvYXBpL2xvZ2luIiwiaWF0IjoxNzExNDAwOTA0LCJleHAiOjE3MTE0MDQ1MDQsIm5iZiI6MTcxMTQwMDkwNCwianRpIjoiaHJNc0Uzbk5SQXlGNlprRCIsInN1YiI6IjIiLCJwcnYiOiIyM2JkNWM4OTQ5ZjYwMGFkYjM5ZTcwMWM0MDA4NzJkYjdhNTk3NmY3In0.ZqcalR7N_vxTRDr5pZi-ECJlGwRZTNHVteqEH-tm-TA"
        }
    }
    ```

- Logout: ``` POST api/auth/logout ```
  
    Retorna:
    ```
    {
        "message": "Logout Successful"
    }
    ```

- Recuperar informações do usuário autenticado: ``` GET api/auth/me ```
  
    Retorna:
    ```
    {
        "id": 2,
        "name": "John Doe",
        "email": "johndoe@email.com",
        "email_verified_at": null,
        "remember_token": null,
        "created_at": "2024-03-25T21:06:25.000000Z",
        "updated_at": "2024-03-25T21:06:25.000000Z"
    }
    ```

<hr>

### Instalação
**Pré-Requisitos:**
- PHP 8.3
- Composer
- Docker

<br>

1. Clone o repositório:
    ```
    git clone https://github.com/matheusmrqs4/jwt-laravel-study
    ```

2. Entre no diretório:
    ```
    cd your-repo
    ```

3. Instale as dependências:
    ```
    composer install
    ```

4. Crie um arquivo .env e preencha os dados:
    ```
    cp .env.example .env
    ```

5. Gere uma nova chave da aplicação:
    ```
    php artisan key:generate
    ```

6. Gere uma nova chave JWT:
    ```
    php artisan jwt:secret
    ```

7. Rode os Containers Docker:
    ```
    docker-compose up -d --build
    ```

8. Acesse em:
    ```
    http://127.0.0.1:8888/
    ```

<hr>

### Testes com PHPUnit
![jwt-tests](public/jwt-tests.png)
