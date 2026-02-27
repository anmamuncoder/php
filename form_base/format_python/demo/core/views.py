from django.shortcuts import render

from django.shortcuts import render

def home(request):
    username = ""
    error = ""

    if request.method == "POST":
        username = request.POST.get("username", "").strip()
        if not username:
            error = "Username is required"

    context = {
        "username": username,
        "error": error,
    }

    return render(request, "home.html", context)
