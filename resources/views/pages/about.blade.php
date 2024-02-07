@extends ('layouts.app')

@section('content')
<div class="main-container">
    <div class="section-box">
        <div class="section-header">
            <h1>About BidHub</h1>
        </div>
        <p class="first-elem">Launched in October 2023, BidHub offers a dynamic web platform designed for individuals from various backgrounds to engage in online auction activities.</p>
        <p> Registered members have the privilege of setting up their own auctions to sell personal items, as well as placing bids on products offered by other users.</p>
        <p> For detailed instructions on bidding and purchasing, please consult our Frequently Asked Questions (<a href="{{ url('/faq') }}">FAQ</a>) section in the home page.</p>
        <p> Should you require additional information or encounter any issues, please do not hesitate to contact our support team at the developers' email addresses provided below. </p>
    </div>
    <div class="section-box">
        <div class="section-header">
            <h2>Contact us</h2>
        </div>
        <ul class="team-list first-elem">
            <li class="team-member">
                <h4>João Sousa - up202106996@up.pt</h4>
                <p>Developer</p>
            </li>
            <li class="team-member">
                <h4>José Oliveira - up202108764@up.pt</h4>
                <p>Developer</p>
            </li>
            <li class="team-member">
                <h4>Pedro Plácido - up202107987@up.pt</h4>
                <p>Developer</p>
            </li>
            <li class="team-member">
                <h4>José Sousa - up202006141@up.pt</h4>
                <p>Developer</p>
            </li>
            
        </ul>
    </div>
</div>
@endsection