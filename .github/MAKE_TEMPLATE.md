# Making This a GitHub Template Repository

To enable the "Use this template" button on GitHub, follow these steps:

## Steps

1. Go to your repository on GitHub: [https://github.com/DanielRSnell/broke-fse](https://github.com/DanielRSnell/broke-fse)

2. Click **Settings** (top navigation bar)

3. Scroll down to the **Template repository** section

4. Check the box: ✅ **"Template repository"**

5. Save changes

## What This Enables

Once marked as a template:

- **"Use this template" button** appears on the repo homepage
- Users can create new repos from this template without forking
- New repos start with clean git history (no boilerplate commits)
- New repos are not connected to the original (can't create PRs back)

## Benefits Over Regular Cloning

| Method | Git History | Connection to Original | Use Case |
|--------|-------------|------------------------|----------|
| **Fork** | Full history | Connected (can PR) | Contributing back to boilerplate |
| **Clone** | Full history | Not connected | Manual cleanup needed |
| **Template** | Clean start | Not connected | ✅ Best for new projects |
| **Degit** | Clean start | Not connected | ✅ CLI alternative |

## User Workflow After Template Enabled

### Via GitHub UI:
1. Click "Use this template" button
2. Name new repository
3. Clone their new repo
4. Run `./setup.sh`
5. Start developing

### Via CLI (Degit):
```bash
npx degit DanielRSnell/broke-fse my-theme
cd my-theme
./setup.sh
```

## Verification

After enabling:
- Visit your repo homepage
- You should see a green **"Use this template"** button next to "Code"
- Button should be in the top-right area of the file browser

## Documentation References

The following files document this workflow:
- **README.md** - Quick Start section
- **.github/TEMPLATE_SETUP.md** - Full setup guide
- **CLAUDE.md** - AI assistant instructions
- **setup.sh** - Automated setup script

## Notes

- Template status doesn't affect existing clones
- Users can still fork or clone normally
- Setup script works with any installation method
- Template feature is GitHub-specific (doesn't affect git itself)

---

**Once enabled, this repo is ready for production use as a WordPress theme boilerplate!**
