@extends('_layouts.master')

@push('meta')
    <meta property="og:title" content="About {{ $page->siteName }}" />
    <meta property="og:type" content="website" />
    <meta property="og:url" content="{{ $page->getUrl() }}"/>
    <meta property="og:description" content="A little bit about {{ $page->siteName }}" />
@endpush

@section('body')
    <div class="container w-full flex flex-col items-center justify-center">
      <div class="max-w-4xl pt-10">
        <h1>Acerca de mí &#128075;&#127995;</h1>

        <img src="/assets/images/main/about-me.jpg"
             alt="Kenny Horna"
             class="flex rounded-full h-64 w-64 bg-contain mx-auto md:float-right my-6 md:ml-10 no-draggable border-2 border-gray-400 hover:shadow-lg"
        >

        <p class="mb-6">¡Hola! Mi nombre es Kenny Horna, Ingeniero de Aplicaciones y Diseñador de Experiencia de Usuario.</p>

        <p class="mb-6">He trabajado en muchos proyectos de software desde la concepción, estrategia, organización y validación, hasta la implementación de las soluciones software para productos y servicios digitales en rubros tales como E-Commerce, Energía, Internet of Things, entre otros.</p>

        <p class="mb-6">Aquí escribiré sobre lo interesante que me tenga atrapado de momento, principalmente desarrollo de software, así como también del día a día y muchos otros temas más. &#128076;&#127995;</p>

        <p class="mb-6">Si deseas contactarme, abajo tienes un listado de medios por donde hacerlo.</p>

      </div>
      <div class="flex mt-10">
        {{-- LinkedIn --}}
        <a href="https://www.linkedin.com/in/kennyhorna">
          <svg class="h-16 border-gray-400 border-2 rounded-full mx-4 hover:shadow-lg cursor-pointer" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M31.9999 0C49.674 0 64 14.328 64 32.0001C64 49.6722 49.674 64 31.9999 64C14.3258 64 0 49.672 0 32.0001C0 14.3282 14.326 0 31.9999 0Z" fill="white"/>
            <path d="M16.0342 46.8182H22.4227V25.5248H16.0342V46.8182ZM40.9227 24.7873C37.8222 24.7873 35.0481 25.9195 33.0801 28.4187V25.4548H26.6682V46.8184H33.0801V35.2655C33.0801 32.824 35.317 30.4421 38.1188 30.4421C40.9206 30.4421 41.6115 32.824 41.6115 35.2058V46.8163H48V34.7303C47.9998 26.3353 44.0252 24.7873 40.9227 24.7873ZM19.1986 23.3969C20.9642 23.3969 22.3971 21.9639 22.3971 20.1983C22.3971 18.4328 20.9642 17 19.1986 17C17.433 17 16 18.433 16 20.1986C16 21.9642 17.433 23.3969 19.1986 23.3969Z" fill="#0E76A8"/>
          </svg>
        </a>
        {{-- Twitter --}}
        <a href="https://twitter.com/kennyhorna">
          <svg class="h-16 border-gray-400 border-2 rounded-full mx-4 hover:shadow-lg cursor-pointer" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M31.9999 0C49.674 0 64 14.328 64 32.0001C64 49.6742 49.674 64 31.9999 64C14.3258 64 0 49.674 0 32.0001C0 14.328 14.326 0 31.9999 0Z" fill="white"/>
            <path d="M48 21.9853C46.8231 22.4938 45.5568 22.8356 44.2284 22.9906C45.5856 22.2008 46.6262 20.9531 47.1161 19.4631C45.8466 20.1955 44.4422 20.726 42.9454 21.0105C41.7482 19.7729 40.0425 19 38.1534 19C34.53 19 31.5901 21.8507 31.5901 25.3696C31.5901 25.8697 31.6475 26.3564 31.7601 26.8228C26.3047 26.5568 21.4656 24.0193 18.2294 20.1652C17.6637 21.1064 17.3404 22.1991 17.3404 23.3677C17.3404 25.5768 18.4987 27.5299 20.26 28.6699C19.1825 28.6363 18.1721 28.3516 17.2849 27.8719V27.9527C17.2849 31.0407 19.5478 33.6152 22.5516 34.2012C22.001 34.346 21.4217 34.4252 20.8223 34.4252C20.3997 34.4252 19.9889 34.3865 19.5881 34.3107C20.4232 36.8431 22.8479 38.685 25.7221 38.7357C23.4744 40.4463 20.6438 41.4634 17.5677 41.4634C17.0372 41.4634 16.5153 41.4331 16 41.3743C18.9078 43.1826 22.3563 44.2383 26.0656 44.2383C38.1416 44.2383 44.7454 34.5263 44.7454 26.1074L44.7269 25.2823C46.0064 24.3864 47.1194 23.265 48 21.9853Z" fill="#26A6D1"/>
          </svg>
        </a>
        {{-- Email --}}
        <a href="mailto:kennyhorna@gmail.com">
          <svg class="h-16 border-gray-400 border-2 rounded-full mx-4 hover:shadow-lg cursor-pointer" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
            <circle cx="32" cy="32" r="32" fill="white"/>
            <path d="M31.9036 16C23.1344 16 16 23.1344 16 31.9036C16 40.7058 23.1197 48 31.9036 48C35.4577 48 39.053 46.8055 41.7676 44.7228C42.4012 44.2367 42.5207 43.3292 42.0346 42.6956C41.5485 42.0621 40.6409 41.9426 40.0074 42.4286C37.7878 44.1317 34.834 45.1277 31.9036 45.1277C24.7288 45.1277 18.8916 39.1848 18.8916 31.9036C18.8916 24.7288 24.7288 18.8723 31.9036 18.8723C39.1848 18.8723 45.1084 24.7288 45.1084 31.9036V33.3494C45.1084 34.9438 43.8113 36.241 42.2169 36.241C40.6225 36.241 39.3253 34.9438 39.3253 33.3494C39.3253 32.6333 39.3253 26.8758 39.3253 26.1205C39.3253 25.322 38.678 24.6747 37.8795 24.6747C37.0811 24.6747 36.4337 25.322 36.4337 26.1205V26.2121C35.1281 25.2166 33.5487 24.6747 31.9036 24.6747C27.9176 24.6747 24.6747 27.9176 24.6747 31.9036C24.6747 35.8896 27.9176 39.1325 31.9036 39.1325C34.0649 39.1325 36.0539 38.2051 37.4684 36.6446C38.514 38.1467 40.252 39.1325 42.2169 39.1325C45.4057 39.1325 48 36.5382 48 33.3494V31.9036C48 23.108 40.6949 16 31.9036 16ZM31.9036 36.2602C29.512 36.2602 27.5663 34.2952 27.5663 31.9036C27.5663 29.512 29.512 27.547 31.9036 27.547C34.3592 27.547 36.4337 29.5525 36.4337 31.9036C36.4337 34.2547 34.3592 36.2602 31.9036 36.2602Z" fill="#9B51E0"/>
          </svg>
        </a>
        {{-- StackOverflow --}}
        <a href="https://stackoverflow.com/story/kennyhorna">
          <svg class="h-16 border-gray-400 border-2 rounded-full mx-4 hover:shadow-lg cursor-pointer" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
            <circle cx="32" cy="32" r="30" fill="white"/>
            <path d="M25.0041 36.6303H37.5827V39.0067H25.0041V36.6303Z" fill="#F48024"/>
            <path d="M25.2554 33.3234L37.4745 35.889L37.9771 33.449L25.758 30.885L25.2554 33.3234Z" fill="#F48024"/>
            <path d="M26.8658 27.4677L27.922 25.2047L39.2366 30.4857L38.1804 32.7486L26.8658 27.4677Z" fill="#F48024"/>
            <path d="M30.0066 21.9091L39.6123 29.9052L41.2204 27.9932L31.5908 19.9988L30.0066 21.9091Z" fill="#F48024"/>
            <path d="M36.1925 16L34.1803 17.484L41.6228 27.5161L43.635 26.032L36.1925 16Z" fill="#F48024"/>
            <path d="M42.5724 34.0168H39.9638V41.3814H22.6134V34.0168H20V43.9964H24.9754V47.9999L28.9806 43.9964H42.5724V34.0168Z" fill="#BCBBBB"/>
          </svg>
        </a>
      </div>
    </div>
@endsection
