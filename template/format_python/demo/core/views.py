from django.shortcuts import render

def home(request):
    context = {
        "username": "Mamun",
        "is_logged_in": True,
        "users": ["Ali", "Rahim", "Karim"]
    }
    return render(request, "home.html", context)
