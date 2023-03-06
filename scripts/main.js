function logout()
{
    if (confirm("Are you sure you want to log out?"))
    {
        window.location.href = "logout.php";
    }
}

function ratePost(rating, vote)
{
    let post = vote.parentNode;
    let postID = post.elements['postID'].value;
    $.ajax({
            type: "POST",
            url: "scripts/displaycontent.php",
            data: { vote: 1, postID: postID, rating: rating }
    })
    setTimeout(function() { updatePost(post); }, 200);
}

async function updatePost(post)
{
    let postID = post.elements['postID'].value;
    let postdiv = post.parentNode;
    let currentrating = postdiv.getElementsByClassName("rating")[0];
    let response = await fetch('scripts/rating.php', {
        method:'POST',
        body: postID
    });
    if (response.status === 200)
    {
        let rating = await response.text();
        if (rating) currentrating.innerHTML = rating;
    }
}

let upvote = document.getElementsByClassName("upvote");
let downvote = document.getElementsByClassName("downvote");

if (upvote)
{
    for (let i = 0; i < upvote.length; i++)
    {
        upvote[i].addEventListener("click", function() { ratePost(1, upvote[i]); }, false)
    }
}

if (downvote)
{
    for (let i = 0; i < upvote.length; i++)
    {
        downvote[i].addEventListener("click", function() { ratePost(-1, downvote[i]); }, false)
    }
}

let logbtn = document.getElementById("logbtn");
if (logbtn) logbtn.addEventListener("click", logout, false);