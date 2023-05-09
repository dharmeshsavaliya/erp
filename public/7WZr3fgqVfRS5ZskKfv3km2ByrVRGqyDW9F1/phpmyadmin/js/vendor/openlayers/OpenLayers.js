/*!
 * OpenLayers v6.14.1 (https://openlayers.org/)
 * Copyright 2005-present, OpenLayers Contributors All rights reserved.
 * Licensed under BSD 2-Clause License (https://github.com/openlayers/openlayers/blob/main/LICENSE.md)
 *
 * @license BSD-2-Clause
 */
!(function (t, e) {
  "object" == typeof exports && "object" == typeof module
    ? (module.exports = e())
    : "function" == typeof define && define.amd
    ? define([], e)
    : "object" == typeof exports
    ? (exports.ol = e())
    : (t.ol = e());
})(self, function () {
  return (function () {
    var t = {
        582: function (t) {
          t.exports = (function () {
            "use strict";
            function t(t, i, r, o, s) {
              !(function t(n, i, r, o, s) {
                for (; o > r; ) {
                  if (o - r > 600) {
                    var a = o - r + 1,
                      l = i - r + 1,
                      h = Math.log(a),
                      u = 0.5 * Math.exp((2 * h) / 3),
                      c =
                        0.5 *
                        Math.sqrt((h * u * (a - u)) / a) *
                        (l - a / 2 < 0 ? -1 : 1);
                    t(
                      n,
                      i,
                      Math.max(r, Math.floor(i - (l * u) / a + c)),
                      Math.min(o, Math.floor(i + ((a - l) * u) / a + c)),
                      s
                    );
                  }
                  var p = n[i],
                    f = r,
                    d = o;
                  for (e(n, r, i), s(n[o], p) > 0 && e(n, r, o); f < d; ) {
                    for (e(n, f, d), f++, d--; s(n[f], p) < 0; ) f++;
                    for (; s(n[d], p) > 0; ) d--;
                  }
                  0 === s(n[r], p) ? e(n, r, d) : e(n, ++d, o),
                    d <= i && (r = d + 1),
                    i <= d && (o = d - 1);
                }
              })(t, i, r || 0, o || t.length - 1, s || n);
            }
            function e(t, e, n) {
              var i = t[e];
              (t[e] = t[n]), (t[n] = i);
            }
            function n(t, e) {
              return t < e ? -1 : t > e ? 1 : 0;
            }
            var i = function (t) {
              void 0 === t && (t = 9),
                (this._maxEntries = Math.max(4, t)),
                (this._minEntries = Math.max(
                  2,
                  Math.ceil(0.4 * this._maxEntries)
                )),
                this.clear();
            };
            function r(t, e, n) {
              if (!n) return e.indexOf(t);
              for (var i = 0; i < e.length; i++) if (n(t, e[i])) return i;
              return -1;
            }
            function o(t, e) {
              s(t, 0, t.children.length, e, t);
            }
            function s(t, e, n, i, r) {
              r || (r = d(null)),
                (r.minX = 1 / 0),
                (r.minY = 1 / 0),
                (r.maxX = -1 / 0),
                (r.maxY = -1 / 0);
              for (var o = e; o < n; o++) {
                var s = t.children[o];
                a(r, t.leaf ? i(s) : s);
              }
              return r;
            }
            function a(t, e) {
              return (
                (t.minX = Math.min(t.minX, e.minX)),
                (t.minY = Math.min(t.minY, e.minY)),
                (t.maxX = Math.max(t.maxX, e.maxX)),
                (t.maxY = Math.max(t.maxY, e.maxY)),
                t
              );
            }
            function l(t, e) {
              return t.minX - e.minX;
            }
            function h(t, e) {
              return t.minY - e.minY;
            }
            function u(t) {
              return (t.maxX - t.minX) * (t.maxY - t.minY);
            }
            function c(t) {
              return t.maxX - t.minX + (t.maxY - t.minY);
            }
            function p(t, e) {
              return (
                t.minX <= e.minX &&
                t.minY <= e.minY &&
                e.maxX <= t.maxX &&
                e.maxY <= t.maxY
              );
            }
            function f(t, e) {
              return (
                e.minX <= t.maxX &&
                e.minY <= t.maxY &&
                e.maxX >= t.minX &&
                e.maxY >= t.minY
              );
            }
            function d(t) {
              return {
                children: t,
                height: 1,
                leaf: !0,
                minX: 1 / 0,
                minY: 1 / 0,
                maxX: -1 / 0,
                maxY: -1 / 0,
              };
            }
            function g(e, n, i, r, o) {
              for (var s = [n, i]; s.length; )
                if (!((i = s.pop()) - (n = s.pop()) <= r)) {
                  var a = n + Math.ceil((i - n) / r / 2) * r;
                  t(e, a, n, i, o), s.push(n, a, a, i);
                }
            }
            return (
              (i.prototype.all = function () {
                return this._all(this.data, []);
              }),
              (i.prototype.search = function (t) {
                var e = this.data,
                  n = [];
                if (!f(t, e)) return n;
                for (var i = this.toBBox, r = []; e; ) {
                  for (var o = 0; o < e.children.length; o++) {
                    var s = e.children[o],
                      a = e.leaf ? i(s) : s;
                    f(t, a) &&
                      (e.leaf
                        ? n.push(s)
                        : p(t, a)
                        ? this._all(s, n)
                        : r.push(s));
                  }
                  e = r.pop();
                }
                return n;
              }),
              (i.prototype.collides = function (t) {
                var e = this.data;
                if (!f(t, e)) return !1;
                for (var n = []; e; ) {
                  for (var i = 0; i < e.children.length; i++) {
                    var r = e.children[i],
                      o = e.leaf ? this.toBBox(r) : r;
                    if (f(t, o)) {
                      if (e.leaf || p(t, o)) return !0;
                      n.push(r);
                    }
                  }
                  e = n.pop();
                }
                return !1;
              }),
              (i.prototype.load = function (t) {
                if (!t || !t.length) return this;
                if (t.length < this._minEntries) {
                  for (var e = 0; e < t.length; e++) this.insert(t[e]);
                  return this;
                }
                var n = this._build(t.slice(), 0, t.length - 1, 0);
                if (this.data.children.length)
                  if (this.data.height === n.height)
                    this._splitRoot(this.data, n);
                  else {
                    if (this.data.height < n.height) {
                      var i = this.data;
                      (this.data = n), (n = i);
                    }
                    this._insert(n, this.data.height - n.height - 1, !0);
                  }
                else this.data = n;
                return this;
              }),
              (i.prototype.insert = function (t) {
                return t && this._insert(t, this.data.height - 1), this;
              }),
              (i.prototype.clear = function () {
                return (this.data = d([])), this;
              }),
              (i.prototype.remove = function (t, e) {
                if (!t) return this;
                for (
                  var n,
                    i,
                    o,
                    s = this.data,
                    a = this.toBBox(t),
                    l = [],
                    h = [];
                  s || l.length;

                ) {
                  if (
                    (s ||
                      ((s = l.pop()),
                      (i = l[l.length - 1]),
                      (n = h.pop()),
                      (o = !0)),
                    s.leaf)
                  ) {
                    var u = r(t, s.children, e);
                    if (-1 !== u)
                      return (
                        s.children.splice(u, 1),
                        l.push(s),
                        this._condense(l),
                        this
                      );
                  }
                  o || s.leaf || !p(s, a)
                    ? i
                      ? (n++, (s = i.children[n]), (o = !1))
                      : (s = null)
                    : (l.push(s),
                      h.push(n),
                      (n = 0),
                      (i = s),
                      (s = s.children[0]));
                }
                return this;
              }),
              (i.prototype.toBBox = function (t) {
                return t;
              }),
              (i.prototype.compareMinX = function (t, e) {
                return t.minX - e.minX;
              }),
              (i.prototype.compareMinY = function (t, e) {
                return t.minY - e.minY;
              }),
              (i.prototype.toJSON = function () {
                return this.data;
              }),
              (i.prototype.fromJSON = function (t) {
                return (this.data = t), this;
              }),
              (i.prototype._all = function (t, e) {
                for (var n = []; t; )
                  t.leaf
                    ? e.push.apply(e, t.children)
                    : n.push.apply(n, t.children),
                    (t = n.pop());
                return e;
              }),
              (i.prototype._build = function (t, e, n, i) {
                var r,
                  s = n - e + 1,
                  a = this._maxEntries;
                if (s <= a)
                  return o((r = d(t.slice(e, n + 1))), this.toBBox), r;
                i ||
                  ((i = Math.ceil(Math.log(s) / Math.log(a))),
                  (a = Math.ceil(s / Math.pow(a, i - 1)))),
                  ((r = d([])).leaf = !1),
                  (r.height = i);
                var l = Math.ceil(s / a),
                  h = l * Math.ceil(Math.sqrt(a));
                g(t, e, n, h, this.compareMinX);
                for (var u = e; u <= n; u += h) {
                  var c = Math.min(u + h - 1, n);
                  g(t, u, c, l, this.compareMinY);
                  for (var p = u; p <= c; p += l) {
                    var f = Math.min(p + l - 1, c);
                    r.children.push(this._build(t, p, f, i - 1));
                  }
                }
                return o(r, this.toBBox), r;
              }),
              (i.prototype._chooseSubtree = function (t, e, n, i) {
                for (; i.push(e), !e.leaf && i.length - 1 !== n; ) {
                  for (
                    var r = 1 / 0, o = 1 / 0, s = void 0, a = 0;
                    a < e.children.length;
                    a++
                  ) {
                    var l = e.children[a],
                      h = u(l),
                      c =
                        ((p = t),
                        (f = l),
                        (Math.max(f.maxX, p.maxX) - Math.min(f.minX, p.minX)) *
                          (Math.max(f.maxY, p.maxY) -
                            Math.min(f.minY, p.minY)) -
                          h);
                    c < o
                      ? ((o = c), (r = h < r ? h : r), (s = l))
                      : c === o && h < r && ((r = h), (s = l));
                  }
                  e = s || e.children[0];
                }
                var p, f;
                return e;
              }),
              (i.prototype._insert = function (t, e, n) {
                var i = n ? t : this.toBBox(t),
                  r = [],
                  o = this._chooseSubtree(i, this.data, e, r);
                for (
                  o.children.push(t), a(o, i);
                  e >= 0 && r[e].children.length > this._maxEntries;

                )
                  this._split(r, e), e--;
                this._adjustParentBBoxes(i, r, e);
              }),
              (i.prototype._split = function (t, e) {
                var n = t[e],
                  i = n.children.length,
                  r = this._minEntries;
                this._chooseSplitAxis(n, r, i);
                var s = this._chooseSplitIndex(n, r, i),
                  a = d(n.children.splice(s, n.children.length - s));
                (a.height = n.height),
                  (a.leaf = n.leaf),
                  o(n, this.toBBox),
                  o(a, this.toBBox),
                  e ? t[e - 1].children.push(a) : this._splitRoot(n, a);
              }),
              (i.prototype._splitRoot = function (t, e) {
                (this.data = d([t, e])),
                  (this.data.height = t.height + 1),
                  (this.data.leaf = !1),
                  o(this.data, this.toBBox);
              }),
              (i.prototype._chooseSplitIndex = function (t, e, n) {
                for (
                  var i, r, o, a, l, h, c, p = 1 / 0, f = 1 / 0, d = e;
                  d <= n - e;
                  d++
                ) {
                  var g = s(t, 0, d, this.toBBox),
                    _ = s(t, d, n, this.toBBox),
                    y =
                      ((r = g),
                      (o = _),
                      void 0,
                      void 0,
                      void 0,
                      void 0,
                      (a = Math.max(r.minX, o.minX)),
                      (l = Math.max(r.minY, o.minY)),
                      (h = Math.min(r.maxX, o.maxX)),
                      (c = Math.min(r.maxY, o.maxY)),
                      Math.max(0, h - a) * Math.max(0, c - l)),
                    v = u(g) + u(_);
                  y < p
                    ? ((p = y), (i = d), (f = v < f ? v : f))
                    : y === p && v < f && ((f = v), (i = d));
                }
                return i || n - e;
              }),
              (i.prototype._chooseSplitAxis = function (t, e, n) {
                var i = t.leaf ? this.compareMinX : l,
                  r = t.leaf ? this.compareMinY : h;
                this._allDistMargin(t, e, n, i) <
                  this._allDistMargin(t, e, n, r) && t.children.sort(i);
              }),
              (i.prototype._allDistMargin = function (t, e, n, i) {
                t.children.sort(i);
                for (
                  var r = this.toBBox,
                    o = s(t, 0, e, r),
                    l = s(t, n - e, n, r),
                    h = c(o) + c(l),
                    u = e;
                  u < n - e;
                  u++
                ) {
                  var p = t.children[u];
                  a(o, t.leaf ? r(p) : p), (h += c(o));
                }
                for (var f = n - e - 1; f >= e; f--) {
                  var d = t.children[f];
                  a(l, t.leaf ? r(d) : d), (h += c(l));
                }
                return h;
              }),
              (i.prototype._adjustParentBBoxes = function (t, e, n) {
                for (var i = n; i >= 0; i--) a(e[i], t);
              }),
              (i.prototype._condense = function (t) {
                for (var e = t.length - 1, n = void 0; e >= 0; e--)
                  0 === t[e].children.length
                    ? e > 0
                      ? (n = t[e - 1].children).splice(n.indexOf(t[e]), 1)
                      : this.clear()
                    : o(t[e], this.toBBox);
              }),
              i
            );
          })();
        },
      },
      e = {};
    function n(i) {
      var r = e[i];
      if (void 0 !== r) return r.exports;
      var o = (e[i] = { exports: {} });
      return t[i].call(o.exports, o, o.exports, n), o.exports;
    }
    (n.d = function (t, e) {
      for (var i in e)
        n.o(e, i) &&
          !n.o(t, i) &&
          Object.defineProperty(t, i, { enumerable: !0, get: e[i] });
    }),
      (n.o = function (t, e) {
        return Object.prototype.hasOwnProperty.call(t, e);
      });
    var i = {};
    return (
      (function () {
        "use strict";
        n.d(i, {
          default: function () {
            return Ch;
          },
        });
        var t = (function () {
            function t(t) {
              this.propagationStopped,
                this.defaultPrevented,
                (this.type = t),
                (this.target = null);
            }
            return (
              (t.prototype.preventDefault = function () {
                this.defaultPrevented = !0;
              }),
              (t.prototype.stopPropagation = function () {
                this.propagationStopped = !0;
              }),
              t
            );
          })(),
          e = "propertychange",
          r = (function () {
            function t() {
              this.disposed = !1;
            }
            return (
              (t.prototype.dispose = function () {
                this.disposed || ((this.disposed = !0), this.disposeInternal());
              }),
              (t.prototype.disposeInternal = function () {}),
              t
            );
          })();
        function o(t, e) {
          return t > e ? 1 : t < e ? -1 : 0;
        }
        function s(t, e, n) {
          var i = t.length;
          if (t[0] <= e) return 0;
          if (e <= t[i - 1]) return i - 1;
          var r = void 0;
          if (n > 0) {
            for (r = 1; r < i; ++r) if (t[r] < e) return r - 1;
          } else if (n < 0) {
            for (r = 1; r < i; ++r) if (t[r] <= e) return r;
          } else
            for (r = 1; r < i; ++r) {
              if (t[r] == e) return r;
              if (t[r] < e)
                return "function" == typeof n
                  ? n(e, t[r - 1], t[r]) > 0
                    ? r - 1
                    : r
                  : t[r - 1] - e < e - t[r]
                  ? r - 1
                  : r;
            }
          return i - 1;
        }
        function a(t, e, n) {
          for (; e < n; ) {
            var i = t[e];
            (t[e] = t[n]), (t[n] = i), ++e, --n;
          }
        }
        function l(t, e) {
          for (
            var n = Array.isArray(e) ? e : [e], i = n.length, r = 0;
            r < i;
            r++
          )
            t[t.length] = n[r];
        }
        function h(t, e) {
          var n = t.length;
          if (n !== e.length) return !1;
          for (var i = 0; i < n; i++) if (t[i] !== e[i]) return !1;
          return !0;
        }
        function u() {
          return !0;
        }
        function c() {
          return !1;
        }
        function p() {}
        var f =
          "function" == typeof Object.assign
            ? Object.assign
            : function (t, e) {
                if (null == t)
                  throw new TypeError(
                    "Cannot convert undefined or null to object"
                  );
                for (
                  var n = Object(t), i = 1, r = arguments.length;
                  i < r;
                  ++i
                ) {
                  var o = arguments[i];
                  if (null != o)
                    for (var s in o) o.hasOwnProperty(s) && (n[s] = o[s]);
                }
                return n;
              };
        function d(t) {
          for (var e in t) delete t[e];
        }
        var g =
          "function" == typeof Object.values
            ? Object.values
            : function (t) {
                var e = [];
                for (var n in t) e.push(t[n]);
                return e;
              };
        function _(t) {
          var e;
          for (e in t) return !1;
          return !e;
        }
        var y,
          v =
            ((y = function (t, e) {
              return (
                (y =
                  Object.setPrototypeOf ||
                  ({ __proto__: [] } instanceof Array &&
                    function (t, e) {
                      t.__proto__ = e;
                    }) ||
                  function (t, e) {
                    for (var n in e)
                      Object.prototype.hasOwnProperty.call(e, n) &&
                        (t[n] = e[n]);
                  }),
                y(t, e)
              );
            }),
            function (t, e) {
              if ("function" != typeof e && null !== e)
                throw new TypeError(
                  "Class extends value " +
                    String(e) +
                    " is not a constructor or null"
                );
              function n() {
                this.constructor = t;
              }
              y(t, e),
                (t.prototype =
                  null === e
                    ? Object.create(e)
                    : ((n.prototype = e.prototype), new n()));
            }),
          m = (function (e) {
            function n(t) {
              var n = e.call(this) || this;
              return (
                (n.eventTarget_ = t),
                (n.pendingRemovals_ = null),
                (n.dispatching_ = null),
                (n.listeners_ = null),
                n
              );
            }
            return (
              v(n, e),
              (n.prototype.addEventListener = function (t, e) {
                if (t && e) {
                  var n = this.listeners_ || (this.listeners_ = {}),
                    i = n[t] || (n[t] = []);
                  -1 === i.indexOf(e) && i.push(e);
                }
              }),
              (n.prototype.dispatchEvent = function (e) {
                var n = "string" == typeof e,
                  i = n ? e : e.type,
                  r = this.listeners_ && this.listeners_[i];
                if (r) {
                  var o = n ? new t(e) : e;
                  o.target || (o.target = this.eventTarget_ || this);
                  var s,
                    a = this.dispatching_ || (this.dispatching_ = {}),
                    l = this.pendingRemovals_ || (this.pendingRemovals_ = {});
                  i in a || ((a[i] = 0), (l[i] = 0)), ++a[i];
                  for (var h = 0, u = r.length; h < u; ++h)
                    if (
                      !1 ===
                        (s =
                          "handleEvent" in r[h]
                            ? r[h].handleEvent(o)
                            : r[h].call(this, o)) ||
                      o.propagationStopped
                    ) {
                      s = !1;
                      break;
                    }
                  if (0 == --a[i]) {
                    var c = l[i];
                    for (delete l[i]; c--; ) this.removeEventListener(i, p);
                    delete a[i];
                  }
                  return s;
                }
              }),
              (n.prototype.disposeInternal = function () {
                this.listeners_ && d(this.listeners_);
              }),
              (n.prototype.getListeners = function (t) {
                return (this.listeners_ && this.listeners_[t]) || void 0;
              }),
              (n.prototype.hasListener = function (t) {
                return (
                  !!this.listeners_ &&
                  (t
                    ? t in this.listeners_
                    : Object.keys(this.listeners_).length > 0)
                );
              }),
              (n.prototype.removeEventListener = function (t, e) {
                var n = this.listeners_ && this.listeners_[t];
                if (n) {
                  var i = n.indexOf(e);
                  -1 !== i &&
                    (this.pendingRemovals_ && t in this.pendingRemovals_
                      ? ((n[i] = p), ++this.pendingRemovals_[t])
                      : (n.splice(i, 1),
                        0 === n.length && delete this.listeners_[t]));
                }
              }),
              n
            );
          })(r),
          x = "change",
          C = "contextmenu",
          w = "click",
          S = "keydown",
          E = "keypress",
          T = "touchmove",
          b = "wheel";
        function O(t, e, n, i, r) {
          if ((i && i !== t && (n = n.bind(i)), r)) {
            var o = n;
            n = function () {
              t.removeEventListener(e, n), o.apply(this, arguments);
            };
          }
          var s = { target: t, type: e, listener: n };
          return t.addEventListener(e, n), s;
        }
        function R(t, e, n, i) {
          return O(t, e, n, i, !0);
        }
        function I(t) {
          t &&
            t.target &&
            (t.target.removeEventListener(t.type, t.listener), d(t));
        }
        var P = (function () {
            var t = function (e, n) {
              return (
                (t =
                  Object.setPrototypeOf ||
                  ({ __proto__: [] } instanceof Array &&
                    function (t, e) {
                      t.__proto__ = e;
                    }) ||
                  function (t, e) {
                    for (var n in e)
                      Object.prototype.hasOwnProperty.call(e, n) &&
                        (t[n] = e[n]);
                  }),
                t(e, n)
              );
            };
            return function (e, n) {
              if ("function" != typeof n && null !== n)
                throw new TypeError(
                  "Class extends value " +
                    String(n) +
                    " is not a constructor or null"
                );
              function i() {
                this.constructor = e;
              }
              t(e, n),
                (e.prototype =
                  null === n
                    ? Object.create(n)
                    : ((i.prototype = n.prototype), new i()));
            };
          })(),
          M = (function (t) {
            function e() {
              var e = t.call(this) || this;
              return (
                (e.on = e.onInternal),
                (e.once = e.onceInternal),
                (e.un = e.unInternal),
                (e.revision_ = 0),
                e
              );
            }
            return (
              P(e, t),
              (e.prototype.changed = function () {
                ++this.revision_, this.dispatchEvent(x);
              }),
              (e.prototype.getRevision = function () {
                return this.revision_;
              }),
              (e.prototype.onInternal = function (t, e) {
                if (Array.isArray(t)) {
                  for (var n = t.length, i = new Array(n), r = 0; r < n; ++r)
                    i[r] = O(this, t[r], e);
                  return i;
                }
                return O(this, t, e);
              }),
              (e.prototype.onceInternal = function (t, e) {
                var n;
                if (Array.isArray(t)) {
                  var i = t.length;
                  n = new Array(i);
                  for (var r = 0; r < i; ++r) n[r] = R(this, t[r], e);
                } else n = R(this, t, e);
                return (e.ol_key = n), n;
              }),
              (e.prototype.unInternal = function (t, e) {
                var n = e.ol_key;
                if (n)
                  !(function (t) {
                    if (Array.isArray(t))
                      for (var e = 0, n = t.length; e < n; ++e) I(t[e]);
                    else I(t);
                  })(n);
                else if (Array.isArray(t))
                  for (var i = 0, r = t.length; i < r; ++i)
                    this.removeEventListener(t[i], e);
                else this.removeEventListener(t, e);
              }),
              e
            );
          })(m);
        M.prototype.on, M.prototype.once, M.prototype.un;
        var F = M;
        function L() {
          return (function () {
            throw new Error("Unimplemented abstract method.");
          })();
        }
        var A = 0;
        function D(t) {
          return t.ol_uid || (t.ol_uid = String(++A));
        }
        var k = (function () {
            var t = function (e, n) {
              return (
                (t =
                  Object.setPrototypeOf ||
                  ({ __proto__: [] } instanceof Array &&
                    function (t, e) {
                      t.__proto__ = e;
                    }) ||
                  function (t, e) {
                    for (var n in e)
                      Object.prototype.hasOwnProperty.call(e, n) &&
                        (t[n] = e[n]);
                  }),
                t(e, n)
              );
            };
            return function (e, n) {
              if ("function" != typeof n && null !== n)
                throw new TypeError(
                  "Class extends value " +
                    String(n) +
                    " is not a constructor or null"
                );
              function i() {
                this.constructor = e;
              }
              t(e, n),
                (e.prototype =
                  null === n
                    ? Object.create(n)
                    : ((i.prototype = n.prototype), new i()));
            };
          })(),
          j = (function (t) {
            function e(e, n, i) {
              var r = t.call(this, e) || this;
              return (r.key = n), (r.oldValue = i), r;
            }
            return k(e, t), e;
          })(t),
          G = (function (t) {
            function n(e) {
              var n = t.call(this) || this;
              return (
                n.on,
                n.once,
                n.un,
                D(n),
                (n.values_ = null),
                void 0 !== e && n.setProperties(e),
                n
              );
            }
            return (
              k(n, t),
              (n.prototype.get = function (t) {
                var e;
                return (
                  this.values_ &&
                    this.values_.hasOwnProperty(t) &&
                    (e = this.values_[t]),
                  e
                );
              }),
              (n.prototype.getKeys = function () {
                return (this.values_ && Object.keys(this.values_)) || [];
              }),
              (n.prototype.getProperties = function () {
                return (this.values_ && f({}, this.values_)) || {};
              }),
              (n.prototype.hasProperties = function () {
                return !!this.values_;
              }),
              (n.prototype.notify = function (t, n) {
                var i;
                (i = "change:".concat(t)),
                  this.hasListener(i) && this.dispatchEvent(new j(i, t, n)),
                  (i = e),
                  this.hasListener(i) && this.dispatchEvent(new j(i, t, n));
              }),
              (n.prototype.addChangeListener = function (t, e) {
                this.addEventListener("change:".concat(t), e);
              }),
              (n.prototype.removeChangeListener = function (t, e) {
                this.removeEventListener("change:".concat(t), e);
              }),
              (n.prototype.set = function (t, e, n) {
                var i = this.values_ || (this.values_ = {});
                if (n) i[t] = e;
                else {
                  var r = i[t];
                  (i[t] = e), r !== e && this.notify(t, r);
                }
              }),
              (n.prototype.setProperties = function (t, e) {
                for (var n in t) this.set(n, t[n], e);
              }),
              (n.prototype.applyProperties = function (t) {
                t.values_ && f(this.values_ || (this.values_ = {}), t.values_);
              }),
              (n.prototype.unset = function (t, e) {
                if (this.values_ && t in this.values_) {
                  var n = this.values_[t];
                  delete this.values_[t],
                    _(this.values_) && (this.values_ = null),
                    e || this.notify(t, n);
                }
              }),
              n
            );
          })(F),
          z = "postrender",
          W = "loadstart",
          X = "loadend",
          N =
            "undefined" != typeof navigator && void 0 !== navigator.userAgent
              ? navigator.userAgent.toLowerCase()
              : "",
          Y = -1 !== N.indexOf("firefox"),
          B =
            (-1 !== N.indexOf("safari") &&
              -1 == N.indexOf("chrom") &&
              (N.indexOf("version/15.4") >= 0 ||
                N.match(/cpu (os|iphone os) 15_4 like mac os x/)),
            -1 !== N.indexOf("webkit") && -1 == N.indexOf("edge")),
          K = -1 !== N.indexOf("macintosh"),
          Z = "undefined" != typeof devicePixelRatio ? devicePixelRatio : 1,
          V =
            "undefined" != typeof WorkerGlobalScope &&
            "undefined" != typeof OffscreenCanvas &&
            self instanceof WorkerGlobalScope,
          U = "undefined" != typeof Image && Image.prototype.decode,
          H = (function () {
            var t = !1;
            try {
              var e = Object.defineProperty({}, "passive", {
                get: function () {
                  t = !0;
                },
              });
              window.addEventListener("_", null, e),
                window.removeEventListener("_", null, e);
            } catch (t) {}
            return t;
          })();
        function q(t, e, n, i) {
          var r;
          return (
            (r =
              n && n.length
                ? n.shift()
                : V
                ? new OffscreenCanvas(t || 300, e || 300)
                : document.createElement("canvas")),
            t && (r.width = t),
            e && (r.height = e),
            r.getContext("2d", i)
          );
        }
        function J(t, e) {
          var n = e.parentNode;
          n && n.replaceChild(t, e);
        }
        function Q(t) {
          return t && t.parentNode ? t.parentNode.removeChild(t) : null;
        }
        var $ = (function () {
            var t = function (e, n) {
              return (
                (t =
                  Object.setPrototypeOf ||
                  ({ __proto__: [] } instanceof Array &&
                    function (t, e) {
                      t.__proto__ = e;
                    }) ||
                  function (t, e) {
                    for (var n in e)
                      Object.prototype.hasOwnProperty.call(e, n) &&
                        (t[n] = e[n]);
                  }),
                t(e, n)
              );
            };
            return function (e, n) {
              if ("function" != typeof n && null !== n)
                throw new TypeError(
                  "Class extends value " +
                    String(n) +
                    " is not a constructor or null"
                );
              function i() {
                this.constructor = e;
              }
              t(e, n),
                (e.prototype =
                  null === n
                    ? Object.create(n)
                    : ((i.prototype = n.prototype), new i()));
            };
          })(),
          tt = (function (t) {
            function e(e) {
              var n = t.call(this) || this,
                i = e.element;
              return (
                !i ||
                  e.target ||
                  i.style.pointerEvents ||
                  (i.style.pointerEvents = "auto"),
                (n.element = i || null),
                (n.target_ = null),
                (n.map_ = null),
                (n.listenerKeys = []),
                e.render && (n.render = e.render),
                e.target && n.setTarget(e.target),
                n
              );
            }
            return (
              $(e, t),
              (e.prototype.disposeInternal = function () {
                Q(this.element), t.prototype.disposeInternal.call(this);
              }),
              (e.prototype.getMap = function () {
                return this.map_;
              }),
              (e.prototype.setMap = function (t) {
                this.map_ && Q(this.element);
                for (var e = 0, n = this.listenerKeys.length; e < n; ++e)
                  I(this.listenerKeys[e]);
                (this.listenerKeys.length = 0),
                  (this.map_ = t),
                  t &&
                    ((this.target_
                      ? this.target_
                      : t.getOverlayContainerStopEvent()
                    ).appendChild(this.element),
                    this.render !== p &&
                      this.listenerKeys.push(O(t, z, this.render, this)),
                    t.render());
              }),
              (e.prototype.render = function (t) {}),
              (e.prototype.setTarget = function (t) {
                this.target_ =
                  "string" == typeof t ? document.getElementById(t) : t;
              }),
              e
            );
          })(G),
          et = "ol-hidden",
          nt = "ol-control",
          it = new RegExp(
            [
              "^\\s*(?=(?:(?:[-a-z]+\\s*){0,2}(italic|oblique))?)",
              "(?=(?:(?:[-a-z]+\\s*){0,2}(small-caps))?)",
              "(?=(?:(?:[-a-z]+\\s*){0,2}(bold(?:er)?|lighter|[1-9]00 ))?)",
              "(?:(?:normal|\\1|\\2|\\3)\\s*){0,3}((?:xx?-)?",
              "(?:small|large)|medium|smaller|larger|[\\.\\d]+(?:\\%|in|[cem]m|ex|p[ctx]))",
              "(?:\\s*\\/\\s*(normal|[\\.\\d]+(?:\\%|in|[cem]m|ex|p[ctx])?))",
              "?\\s*([-,\\\"\\'\\sa-z]+?)\\s*$",
            ].join(""),
            "i"
          ),
          rt = ["style", "variant", "weight", "size", "lineHeight", "family"],
          ot = function (t) {
            var e = t.match(it);
            if (!e) return null;
            for (
              var n = {
                  lineHeight: "normal",
                  size: "1.2em",
                  style: "normal",
                  weight: "normal",
                  variant: "normal",
                },
                i = 0,
                r = rt.length;
              i < r;
              ++i
            ) {
              var o = e[i + 1];
              void 0 !== o && (n[rt[i]] = o);
            }
            return (n.families = n.family.split(/,\s?/)), n;
          };
        function st(t) {
          return 1 === t ? "" : String(Math.round(100 * t) / 100);
        }
        var at = "opacity",
          lt = "visible",
          ht = "extent",
          ut = "zIndex",
          ct = "maxResolution",
          pt = "minResolution",
          ft = "maxZoom",
          dt = "minZoom",
          gt = "source",
          _t = (function () {
            var t = function (e, n) {
              return (
                (t =
                  Object.setPrototypeOf ||
                  ({ __proto__: [] } instanceof Array &&
                    function (t, e) {
                      t.__proto__ = e;
                    }) ||
                  function (t, e) {
                    for (var n in e)
                      Object.prototype.hasOwnProperty.call(e, n) &&
                        (t[n] = e[n]);
                  }),
                t(e, n)
              );
            };
            return function (e, n) {
              if ("function" != typeof n && null !== n)
                throw new TypeError(
                  "Class extends value " +
                    String(n) +
                    " is not a constructor or null"
                );
              function i() {
                this.constructor = e;
              }
              t(e, n),
                (e.prototype =
                  null === n
                    ? Object.create(n)
                    : ((i.prototype = n.prototype), new i()));
            };
          })(),
          yt = (function (t) {
            function e(e) {
              var n = this,
                i =
                  "Assertion failed. See https://openlayers.org/en/v" +
                  "6.14.1".split("-")[0] +
                  "/doc/errors/#" +
                  e +
                  " for details.";
              return (
                ((n = t.call(this, i) || this).code = e),
                (n.name = "AssertionError"),
                (n.message = i),
                n
              );
            }
            return _t(e, t), e;
          })(Error);
        function vt(t, e) {
          if (!t) throw new yt(e);
        }
        function mt(t, e, n) {
          return Math.min(Math.max(t, e), n);
        }
        var xt =
            "cosh" in Math
              ? Math.cosh
              : function (t) {
                  var e = Math.exp(t);
                  return (e + 1 / e) / 2;
                },
          Ct =
            "log2" in Math
              ? Math.log2
              : function (t) {
                  return Math.log(t) * Math.LOG2E;
                };
        function wt(t, e, n, i, r, o) {
          var s = r - n,
            a = o - i;
          if (0 !== s || 0 !== a) {
            var l = ((t - n) * s + (e - i) * a) / (s * s + a * a);
            l > 1 ? ((n = r), (i = o)) : l > 0 && ((n += s * l), (i += a * l));
          }
          return St(t, e, n, i);
        }
        function St(t, e, n, i) {
          var r = n - t,
            o = i - e;
          return r * r + o * o;
        }
        function Et(t) {
          return (t * Math.PI) / 180;
        }
        function Tt(t, e) {
          var n = t % e;
          return n * e < 0 ? n + e : n;
        }
        function bt(t, e, n) {
          return t + n * (e - t);
        }
        function Ot(t, e) {
          var n = Math.pow(10, e);
          return Math.round(t * n) / n;
        }
        function Rt(t, e) {
          return Math.floor(Ot(t, e));
        }
        function It(t, e) {
          return Math.ceil(Ot(t, e));
        }
        var Pt = (function () {
            var t = function (e, n) {
              return (
                (t =
                  Object.setPrototypeOf ||
                  ({ __proto__: [] } instanceof Array &&
                    function (t, e) {
                      t.__proto__ = e;
                    }) ||
                  function (t, e) {
                    for (var n in e)
                      Object.prototype.hasOwnProperty.call(e, n) &&
                        (t[n] = e[n]);
                  }),
                t(e, n)
              );
            };
            return function (e, n) {
              if ("function" != typeof n && null !== n)
                throw new TypeError(
                  "Class extends value " +
                    String(n) +
                    " is not a constructor or null"
                );
              function i() {
                this.constructor = e;
              }
              t(e, n),
                (e.prototype =
                  null === n
                    ? Object.create(n)
                    : ((i.prototype = n.prototype), new i()));
            };
          })(),
          Mt = (function (t) {
            function e(e) {
              var n = t.call(this) || this;
              n.on, n.once, n.un, (n.background_ = e.background);
              var i = f({}, e);
              return (
                "object" == typeof e.properties &&
                  (delete i.properties, f(i, e.properties)),
                (i.opacity = void 0 !== e.opacity ? e.opacity : 1),
                vt("number" == typeof i.opacity, 64),
                (i.visible = void 0 === e.visible || e.visible),
                (i.zIndex = e.zIndex),
                (i.maxResolution =
                  void 0 !== e.maxResolution ? e.maxResolution : 1 / 0),
                (i.minResolution =
                  void 0 !== e.minResolution ? e.minResolution : 0),
                (i.minZoom = void 0 !== e.minZoom ? e.minZoom : -1 / 0),
                (i.maxZoom = void 0 !== e.maxZoom ? e.maxZoom : 1 / 0),
                (n.className_ =
                  void 0 !== i.className ? i.className : "ol-layer"),
                delete i.className,
                n.setProperties(i),
                (n.state_ = null),
                n
              );
            }
            return (
              Pt(e, t),
              (e.prototype.getBackground = function () {
                return this.background_;
              }),
              (e.prototype.getClassName = function () {
                return this.className_;
              }),
              (e.prototype.getLayerState = function (t) {
                var e = this.state_ || {
                    layer: this,
                    managed: void 0 === t || t,
                  },
                  n = this.getZIndex();
                return (
                  (e.opacity = mt(
                    Math.round(100 * this.getOpacity()) / 100,
                    0,
                    1
                  )),
                  (e.visible = this.getVisible()),
                  (e.extent = this.getExtent()),
                  (e.zIndex = void 0 !== n || e.managed ? n : 1 / 0),
                  (e.maxResolution = this.getMaxResolution()),
                  (e.minResolution = Math.max(this.getMinResolution(), 0)),
                  (e.minZoom = this.getMinZoom()),
                  (e.maxZoom = this.getMaxZoom()),
                  (this.state_ = e),
                  e
                );
              }),
              (e.prototype.getLayersArray = function (t) {
                return L();
              }),
              (e.prototype.getLayerStatesArray = function (t) {
                return L();
              }),
              (e.prototype.getExtent = function () {
                return this.get(ht);
              }),
              (e.prototype.getMaxResolution = function () {
                return this.get(ct);
              }),
              (e.prototype.getMinResolution = function () {
                return this.get(pt);
              }),
              (e.prototype.getMinZoom = function () {
                return this.get(dt);
              }),
              (e.prototype.getMaxZoom = function () {
                return this.get(ft);
              }),
              (e.prototype.getOpacity = function () {
                return this.get(at);
              }),
              (e.prototype.getSourceState = function () {
                return L();
              }),
              (e.prototype.getVisible = function () {
                return this.get(lt);
              }),
              (e.prototype.getZIndex = function () {
                return this.get(ut);
              }),
              (e.prototype.setBackground = function (t) {
                (this.background_ = t), this.changed();
              }),
              (e.prototype.setExtent = function (t) {
                this.set(ht, t);
              }),
              (e.prototype.setMaxResolution = function (t) {
                this.set(ct, t);
              }),
              (e.prototype.setMinResolution = function (t) {
                this.set(pt, t);
              }),
              (e.prototype.setMaxZoom = function (t) {
                this.set(ft, t);
              }),
              (e.prototype.setMinZoom = function (t) {
                this.set(dt, t);
              }),
              (e.prototype.setOpacity = function (t) {
                vt("number" == typeof t, 64), this.set(at, t);
              }),
              (e.prototype.setVisible = function (t) {
                this.set(lt, t);
              }),
              (e.prototype.setZIndex = function (t) {
                this.set(ut, t);
              }),
              (e.prototype.disposeInternal = function () {
                this.state_ &&
                  ((this.state_.layer = null), (this.state_ = null)),
                  t.prototype.disposeInternal.call(this);
              }),
              e
            );
          })(G),
          Ft = "precompose",
          Lt = "rendercomplete",
          At = "undefined",
          Dt = "ready",
          kt = (function () {
            var t = function (e, n) {
              return (
                (t =
                  Object.setPrototypeOf ||
                  ({ __proto__: [] } instanceof Array &&
                    function (t, e) {
                      t.__proto__ = e;
                    }) ||
                  function (t, e) {
                    for (var n in e)
                      Object.prototype.hasOwnProperty.call(e, n) &&
                        (t[n] = e[n]);
                  }),
                t(e, n)
              );
            };
            return function (e, n) {
              if ("function" != typeof n && null !== n)
                throw new TypeError(
                  "Class extends value " +
                    String(n) +
                    " is not a constructor or null"
                );
              function i() {
                this.constructor = e;
              }
              t(e, n),
                (e.prototype =
                  null === n
                    ? Object.create(n)
                    : ((i.prototype = n.prototype), new i()));
            };
          })();
        function jt(t, e) {
          if (!t.visible) return !1;
          var n = e.resolution;
          if (n < t.minResolution || n >= t.maxResolution) return !1;
          var i = e.zoom;
          return i > t.minZoom && i <= t.maxZoom;
        }
        var Gt = (function (t) {
            function e(e) {
              var n = this,
                i = f({}, e);
              delete i.source,
                (n = t.call(this, i) || this).on,
                n.once,
                n.un,
                (n.mapPrecomposeKey_ = null),
                (n.mapRenderKey_ = null),
                (n.sourceChangeKey_ = null),
                (n.renderer_ = null),
                (n.rendered = !1),
                e.render && (n.render = e.render),
                e.map && n.setMap(e.map),
                n.addChangeListener(gt, n.handleSourcePropertyChange_);
              var r = e.source ? e.source : null;
              return n.setSource(r), n;
            }
            return (
              kt(e, t),
              (e.prototype.getLayersArray = function (t) {
                var e = t || [];
                return e.push(this), e;
              }),
              (e.prototype.getLayerStatesArray = function (t) {
                var e = t || [];
                return e.push(this.getLayerState()), e;
              }),
              (e.prototype.getSource = function () {
                return this.get(gt) || null;
              }),
              (e.prototype.getRenderSource = function () {
                return this.getSource();
              }),
              (e.prototype.getSourceState = function () {
                var t = this.getSource();
                return t ? t.getState() : At;
              }),
              (e.prototype.handleSourceChange_ = function () {
                this.changed();
              }),
              (e.prototype.handleSourcePropertyChange_ = function () {
                this.sourceChangeKey_ &&
                  (I(this.sourceChangeKey_), (this.sourceChangeKey_ = null));
                var t = this.getSource();
                t &&
                  (this.sourceChangeKey_ = O(
                    t,
                    x,
                    this.handleSourceChange_,
                    this
                  )),
                  this.changed();
              }),
              (e.prototype.getFeatures = function (t) {
                return this.renderer_
                  ? this.renderer_.getFeatures(t)
                  : new Promise(function (t) {
                      return t([]);
                    });
              }),
              (e.prototype.getData = function (t) {
                return this.renderer_ && this.rendered
                  ? this.renderer_.getData(t)
                  : null;
              }),
              (e.prototype.render = function (t, e) {
                var n = this.getRenderer();
                if (n.prepareFrame(t))
                  return (this.rendered = !0), n.renderFrame(t, e);
              }),
              (e.prototype.unrender = function () {
                this.rendered = !1;
              }),
              (e.prototype.setMapInternal = function (t) {
                t || this.unrender(), this.set("map", t);
              }),
              (e.prototype.getMapInternal = function () {
                return this.get("map");
              }),
              (e.prototype.setMap = function (t) {
                this.mapPrecomposeKey_ &&
                  (I(this.mapPrecomposeKey_), (this.mapPrecomposeKey_ = null)),
                  t || this.changed(),
                  this.mapRenderKey_ &&
                    (I(this.mapRenderKey_), (this.mapRenderKey_ = null)),
                  t &&
                    ((this.mapPrecomposeKey_ = O(
                      t,
                      Ft,
                      function (t) {
                        var e = t.frameState.layerStatesArray,
                          n = this.getLayerState(!1);
                        vt(
                          !e.some(function (t) {
                            return t.layer === n.layer;
                          }),
                          67
                        ),
                          e.push(n);
                      },
                      this
                    )),
                    (this.mapRenderKey_ = O(this, x, t.render, t)),
                    this.changed());
              }),
              (e.prototype.setSource = function (t) {
                this.set(gt, t);
              }),
              (e.prototype.getRenderer = function () {
                return (
                  this.renderer_ || (this.renderer_ = this.createRenderer()),
                  this.renderer_
                );
              }),
              (e.prototype.hasRenderer = function () {
                return !!this.renderer_;
              }),
              (e.prototype.createRenderer = function () {
                return null;
              }),
              (e.prototype.disposeInternal = function () {
                this.renderer_ &&
                  (this.renderer_.dispose(), delete this.renderer_),
                  this.setSource(null),
                  t.prototype.disposeInternal.call(this);
              }),
              e
            );
          })(Mt),
          zt = (function () {
            var t = function (e, n) {
              return (
                (t =
                  Object.setPrototypeOf ||
                  ({ __proto__: [] } instanceof Array &&
                    function (t, e) {
                      t.__proto__ = e;
                    }) ||
                  function (t, e) {
                    for (var n in e)
                      Object.prototype.hasOwnProperty.call(e, n) &&
                        (t[n] = e[n]);
                  }),
                t(e, n)
              );
            };
            return function (e, n) {
              if ("function" != typeof n && null !== n)
                throw new TypeError(
                  "Class extends value " +
                    String(n) +
                    " is not a constructor or null"
                );
              function i() {
                this.constructor = e;
              }
              t(e, n),
                (e.prototype =
                  null === n
                    ? Object.create(n)
                    : ((i.prototype = n.prototype), new i()));
            };
          })(),
          Wt = (function (t) {
            function e(e) {
              var n = this,
                i = e || {};
              ((n =
                t.call(this, {
                  element: document.createElement("div"),
                  render: i.render,
                  target: i.target,
                }) || this).ulElement_ = document.createElement("ul")),
                (n.collapsed_ = void 0 === i.collapsed || i.collapsed),
                (n.userCollapsed_ = n.collapsed_),
                (n.overrideCollapsible_ = void 0 !== i.collapsible),
                (n.collapsible_ = void 0 === i.collapsible || i.collapsible),
                n.collapsible_ || (n.collapsed_ = !1);
              var r = void 0 !== i.className ? i.className : "ol-attribution",
                o = void 0 !== i.tipLabel ? i.tipLabel : "Attributions",
                s =
                  void 0 !== i.expandClassName
                    ? i.expandClassName
                    : r + "-expand",
                a = void 0 !== i.collapseLabel ? i.collapseLabel : "›",
                l =
                  void 0 !== i.collapseClassName
                    ? i.collapseClassName
                    : r + "-collapse";
              "string" == typeof a
                ? ((n.collapseLabel_ = document.createElement("span")),
                  (n.collapseLabel_.textContent = a),
                  (n.collapseLabel_.className = l))
                : (n.collapseLabel_ = a);
              var h = void 0 !== i.label ? i.label : "i";
              "string" == typeof h
                ? ((n.label_ = document.createElement("span")),
                  (n.label_.textContent = h),
                  (n.label_.className = s))
                : (n.label_ = h);
              var u =
                n.collapsible_ && !n.collapsed_ ? n.collapseLabel_ : n.label_;
              (n.toggleButton_ = document.createElement("button")),
                n.toggleButton_.setAttribute("type", "button"),
                n.toggleButton_.setAttribute(
                  "aria-expanded",
                  String(!n.collapsed_)
                ),
                (n.toggleButton_.title = o),
                n.toggleButton_.appendChild(u),
                n.toggleButton_.addEventListener(w, n.handleClick_.bind(n), !1);
              var c =
                  r +
                  " ol-unselectable " +
                  nt +
                  (n.collapsed_ && n.collapsible_ ? " ol-collapsed" : "") +
                  (n.collapsible_ ? "" : " ol-uncollapsible"),
                p = n.element;
              return (
                (p.className = c),
                p.appendChild(n.toggleButton_),
                p.appendChild(n.ulElement_),
                (n.renderedAttributions_ = []),
                (n.renderedVisible_ = !0),
                n
              );
            }
            return (
              zt(e, t),
              (e.prototype.collectSourceAttributions_ = function (t) {
                for (
                  var e = {},
                    n = [],
                    i = !0,
                    r = t.layerStatesArray,
                    o = 0,
                    s = r.length;
                  o < s;
                  ++o
                ) {
                  var a = r[o];
                  if (jt(a, t.viewState)) {
                    var l = a.layer.getSource();
                    if (l) {
                      var h = l.getAttributions();
                      if (h) {
                        var u = h(t);
                        if (u)
                          if (
                            ((i = i && !1 !== l.getAttributionsCollapsible()),
                            Array.isArray(u))
                          )
                            for (var c = 0, p = u.length; c < p; ++c)
                              u[c] in e || (n.push(u[c]), (e[u[c]] = !0));
                          else u in e || (n.push(u), (e[u] = !0));
                      }
                    }
                  }
                }
                return this.overrideCollapsible_ || this.setCollapsible(i), n;
              }),
              (e.prototype.updateElement_ = function (t) {
                if (t) {
                  var e = this.collectSourceAttributions_(t),
                    n = e.length > 0;
                  if (
                    (this.renderedVisible_ != n &&
                      ((this.element.style.display = n ? "" : "none"),
                      (this.renderedVisible_ = n)),
                    !h(e, this.renderedAttributions_))
                  ) {
                    !(function (t) {
                      for (; t.lastChild; ) t.removeChild(t.lastChild);
                    })(this.ulElement_);
                    for (var i = 0, r = e.length; i < r; ++i) {
                      var o = document.createElement("li");
                      (o.innerHTML = e[i]), this.ulElement_.appendChild(o);
                    }
                    this.renderedAttributions_ = e;
                  }
                } else
                  this.renderedVisible_ &&
                    ((this.element.style.display = "none"),
                    (this.renderedVisible_ = !1));
              }),
              (e.prototype.handleClick_ = function (t) {
                t.preventDefault(),
                  this.handleToggle_(),
                  (this.userCollapsed_ = this.collapsed_);
              }),
              (e.prototype.handleToggle_ = function () {
                this.element.classList.toggle("ol-collapsed"),
                  this.collapsed_
                    ? J(this.collapseLabel_, this.label_)
                    : J(this.label_, this.collapseLabel_),
                  (this.collapsed_ = !this.collapsed_),
                  this.toggleButton_.setAttribute(
                    "aria-expanded",
                    String(!this.collapsed_)
                  );
              }),
              (e.prototype.getCollapsible = function () {
                return this.collapsible_;
              }),
              (e.prototype.setCollapsible = function (t) {
                this.collapsible_ !== t &&
                  ((this.collapsible_ = t),
                  this.element.classList.toggle("ol-uncollapsible"),
                  this.userCollapsed_ && this.handleToggle_());
              }),
              (e.prototype.setCollapsed = function (t) {
                (this.userCollapsed_ = t),
                  this.collapsible_ &&
                    this.collapsed_ !== t &&
                    this.handleToggle_();
              }),
              (e.prototype.getCollapsed = function () {
                return this.collapsed_;
              }),
              (e.prototype.render = function (t) {
                this.updateElement_(t.frameState);
              }),
              e
            );
          })(tt),
          Xt = "pointermove",
          Nt = "pointerdown",
          Yt = {
            RADIANS: "radians",
            DEGREES: "degrees",
            FEET: "ft",
            METERS: "m",
            PIXELS: "pixels",
            TILE_PIXELS: "tile-pixels",
            USFEET: "us-ft",
          },
          Bt = {};
        (Bt[Yt.RADIANS] = 6370997 / (2 * Math.PI)),
          (Bt[Yt.DEGREES] = (2 * Math.PI * 6370997) / 360),
          (Bt[Yt.FEET] = 0.3048),
          (Bt[Yt.METERS] = 1),
          (Bt[Yt.USFEET] = 1200 / 3937);
        var Kt = Yt,
          Zt = (function () {
            function t(t) {
              (this.code_ = t.code),
                (this.units_ = t.units),
                (this.extent_ = void 0 !== t.extent ? t.extent : null),
                (this.worldExtent_ =
                  void 0 !== t.worldExtent ? t.worldExtent : null),
                (this.axisOrientation_ =
                  void 0 !== t.axisOrientation ? t.axisOrientation : "enu"),
                (this.global_ = void 0 !== t.global && t.global),
                (this.canWrapX_ = !(!this.global_ || !this.extent_)),
                (this.getPointResolutionFunc_ = t.getPointResolution),
                (this.defaultTileGrid_ = null),
                (this.metersPerUnit_ = t.metersPerUnit);
            }
            return (
              (t.prototype.canWrapX = function () {
                return this.canWrapX_;
              }),
              (t.prototype.getCode = function () {
                return this.code_;
              }),
              (t.prototype.getExtent = function () {
                return this.extent_;
              }),
              (t.prototype.getUnits = function () {
                return this.units_;
              }),
              (t.prototype.getMetersPerUnit = function () {
                return this.metersPerUnit_ || Bt[this.units_];
              }),
              (t.prototype.getWorldExtent = function () {
                return this.worldExtent_;
              }),
              (t.prototype.getAxisOrientation = function () {
                return this.axisOrientation_;
              }),
              (t.prototype.isGlobal = function () {
                return this.global_;
              }),
              (t.prototype.setGlobal = function (t) {
                (this.global_ = t), (this.canWrapX_ = !(!t || !this.extent_));
              }),
              (t.prototype.getDefaultTileGrid = function () {
                return this.defaultTileGrid_;
              }),
              (t.prototype.setDefaultTileGrid = function (t) {
                this.defaultTileGrid_ = t;
              }),
              (t.prototype.setExtent = function (t) {
                (this.extent_ = t), (this.canWrapX_ = !(!this.global_ || !t));
              }),
              (t.prototype.setWorldExtent = function (t) {
                this.worldExtent_ = t;
              }),
              (t.prototype.setGetPointResolution = function (t) {
                this.getPointResolutionFunc_ = t;
              }),
              (t.prototype.getPointResolutionFunc = function () {
                return this.getPointResolutionFunc_;
              }),
              t
            );
          })(),
          Vt = (function () {
            var t = function (e, n) {
              return (
                (t =
                  Object.setPrototypeOf ||
                  ({ __proto__: [] } instanceof Array &&
                    function (t, e) {
                      t.__proto__ = e;
                    }) ||
                  function (t, e) {
                    for (var n in e)
                      Object.prototype.hasOwnProperty.call(e, n) &&
                        (t[n] = e[n]);
                  }),
                t(e, n)
              );
            };
            return function (e, n) {
              if ("function" != typeof n && null !== n)
                throw new TypeError(
                  "Class extends value " +
                    String(n) +
                    " is not a constructor or null"
                );
              function i() {
                this.constructor = e;
              }
              t(e, n),
                (e.prototype =
                  null === n
                    ? Object.create(n)
                    : ((i.prototype = n.prototype), new i()));
            };
          })(),
          Ut = 6378137,
          Ht = Math.PI * Ut,
          qt = [-Ht, -Ht, Ht, Ht],
          Jt = [-180, -85, 180, 85],
          Qt = Ut * Math.log(Math.tan(Math.PI / 2)),
          $t = (function (t) {
            function e(e) {
              return (
                t.call(this, {
                  code: e,
                  units: Kt.METERS,
                  extent: qt,
                  global: !0,
                  worldExtent: Jt,
                  getPointResolution: function (t, e) {
                    return t / xt(e[1] / Ut);
                  },
                }) || this
              );
            }
            return Vt(e, t), e;
          })(Zt),
          te = [
            new $t("EPSG:3857"),
            new $t("EPSG:102100"),
            new $t("EPSG:102113"),
            new $t("EPSG:900913"),
            new $t("http://www.opengis.net/def/crs/EPSG/0/3857"),
            new $t("http://www.opengis.net/gml/srs/epsg.xml#3857"),
          ];
        var ee = (function () {
            var t = function (e, n) {
              return (
                (t =
                  Object.setPrototypeOf ||
                  ({ __proto__: [] } instanceof Array &&
                    function (t, e) {
                      t.__proto__ = e;
                    }) ||
                  function (t, e) {
                    for (var n in e)
                      Object.prototype.hasOwnProperty.call(e, n) &&
                        (t[n] = e[n]);
                  }),
                t(e, n)
              );
            };
            return function (e, n) {
              if ("function" != typeof n && null !== n)
                throw new TypeError(
                  "Class extends value " +
                    String(n) +
                    " is not a constructor or null"
                );
              function i() {
                this.constructor = e;
              }
              t(e, n),
                (e.prototype =
                  null === n
                    ? Object.create(n)
                    : ((i.prototype = n.prototype), new i()));
            };
          })(),
          ne = [-180, -90, 180, 90],
          ie = (6378137 * Math.PI) / 180,
          re = (function (t) {
            function e(e, n) {
              return (
                t.call(this, {
                  code: e,
                  units: Kt.DEGREES,
                  extent: ne,
                  axisOrientation: n,
                  global: !0,
                  metersPerUnit: ie,
                  worldExtent: ne,
                }) || this
              );
            }
            return ee(e, t), e;
          })(Zt),
          oe = [
            new re("CRS:84"),
            new re("EPSG:4326", "neu"),
            new re("urn:ogc:def:crs:OGC:1.3:CRS84"),
            new re("urn:ogc:def:crs:OGC:2:84"),
            new re("http://www.opengis.net/def/crs/OGC/1.3/CRS84"),
            new re("http://www.opengis.net/gml/srs/epsg.xml#4326", "neu"),
            new re("http://www.opengis.net/def/crs/EPSG/0/4326", "neu"),
          ],
          se = {},
          ae = {};
        function le(t, e, n) {
          var i = t.getCode(),
            r = e.getCode();
          i in ae || (ae[i] = {}), (ae[i][r] = n);
        }
        var he = "top-left";
        function ue(t) {
          for (
            var e = [1 / 0, 1 / 0, -1 / 0, -1 / 0], n = 0, i = t.length;
            n < i;
            ++n
          )
            we(e, t[n]);
          return e;
        }
        function ce(t, e, n) {
          return n
            ? ((n[0] = t[0] - e),
              (n[1] = t[1] - e),
              (n[2] = t[2] + e),
              (n[3] = t[3] + e),
              n)
            : [t[0] - e, t[1] - e, t[2] + e, t[3] + e];
        }
        function pe(t, e) {
          return e
            ? ((e[0] = t[0]), (e[1] = t[1]), (e[2] = t[2]), (e[3] = t[3]), e)
            : t.slice();
        }
        function fe(t, e, n) {
          var i, r;
          return (
            (i = e < t[0] ? t[0] - e : t[2] < e ? e - t[2] : 0) * i +
            (r = n < t[1] ? t[1] - n : t[3] < n ? n - t[3] : 0) * r
          );
        }
        function de(t, e) {
          return _e(t, e[0], e[1]);
        }
        function ge(t, e) {
          return t[0] <= e[0] && e[2] <= t[2] && t[1] <= e[1] && e[3] <= t[3];
        }
        function _e(t, e, n) {
          return t[0] <= e && e <= t[2] && t[1] <= n && n <= t[3];
        }
        function ye(t, e) {
          var n = t[0],
            i = t[1],
            r = t[2],
            o = t[3],
            s = e[0],
            a = e[1],
            l = 0;
          return (
            s < n ? (l |= 16) : s > r && (l |= 4),
            a < i ? (l |= 8) : a > o && (l |= 2),
            0 === l && (l = 1),
            l
          );
        }
        function ve(t, e, n, i, r) {
          return r
            ? ((r[0] = t), (r[1] = e), (r[2] = n), (r[3] = i), r)
            : [t, e, n, i];
        }
        function me(t) {
          return ve(1 / 0, 1 / 0, -1 / 0, -1 / 0, t);
        }
        function xe(t, e, n, i, r) {
          return Se(me(r), t, e, n, i);
        }
        function Ce(t, e) {
          return t[0] == e[0] && t[2] == e[2] && t[1] == e[1] && t[3] == e[3];
        }
        function we(t, e) {
          e[0] < t[0] && (t[0] = e[0]),
            e[0] > t[2] && (t[2] = e[0]),
            e[1] < t[1] && (t[1] = e[1]),
            e[1] > t[3] && (t[3] = e[1]);
        }
        function Se(t, e, n, i, r) {
          for (; n < i; n += r) Ee(t, e[n], e[n + 1]);
          return t;
        }
        function Ee(t, e, n) {
          (t[0] = Math.min(t[0], e)),
            (t[1] = Math.min(t[1], n)),
            (t[2] = Math.max(t[2], e)),
            (t[3] = Math.max(t[3], n));
        }
        function Te(t, e) {
          var n;
          return (n = e(Oe(t))) || (n = e(Re(t))) || (n = e(De(t)))
            ? n
            : (n = e(Ae(t))) || !1;
        }
        function be(t) {
          var e = 0;
          return Ge(t) || (e = ke(t) * Fe(t)), e;
        }
        function Oe(t) {
          return [t[0], t[1]];
        }
        function Re(t) {
          return [t[2], t[1]];
        }
        function Ie(t) {
          return [(t[0] + t[2]) / 2, (t[1] + t[3]) / 2];
        }
        function Pe(t, e) {
          var n;
          return (
            "bottom-left" === e
              ? (n = Oe(t))
              : "bottom-right" === e
              ? (n = Re(t))
              : e === he
              ? (n = Ae(t))
              : "top-right" === e
              ? (n = De(t))
              : vt(!1, 13),
            n
          );
        }
        function Me(t, e, n, i, r) {
          var o = (e * i[0]) / 2,
            s = (e * i[1]) / 2,
            a = Math.cos(n),
            l = Math.sin(n),
            h = o * a,
            u = o * l,
            c = s * a,
            p = s * l,
            f = t[0],
            d = t[1],
            g = f - h + p,
            _ = f - h - p,
            y = f + h - p,
            v = f + h + p,
            m = d - u - c,
            x = d - u + c,
            C = d + u + c,
            w = d + u - c;
          return ve(
            Math.min(g, _, y, v),
            Math.min(m, x, C, w),
            Math.max(g, _, y, v),
            Math.max(m, x, C, w),
            r
          );
        }
        function Fe(t) {
          return t[3] - t[1];
        }
        function Le(t, e, n) {
          var i = n || [1 / 0, 1 / 0, -1 / 0, -1 / 0];
          return (
            je(t, e)
              ? (t[0] > e[0] ? (i[0] = t[0]) : (i[0] = e[0]),
                t[1] > e[1] ? (i[1] = t[1]) : (i[1] = e[1]),
                t[2] < e[2] ? (i[2] = t[2]) : (i[2] = e[2]),
                t[3] < e[3] ? (i[3] = t[3]) : (i[3] = e[3]))
              : me(i),
            i
          );
        }
        function Ae(t) {
          return [t[0], t[3]];
        }
        function De(t) {
          return [t[2], t[3]];
        }
        function ke(t) {
          return t[2] - t[0];
        }
        function je(t, e) {
          return t[0] <= e[2] && t[2] >= e[0] && t[1] <= e[3] && t[3] >= e[1];
        }
        function Ge(t) {
          return t[2] < t[0] || t[3] < t[1];
        }
        function ze(t, e) {
          for (var n = !0, i = t.length - 1; i >= 0; --i)
            if (t[i] != e[i]) {
              n = !1;
              break;
            }
          return n;
        }
        function We(t, e) {
          var n = Math.cos(e),
            i = Math.sin(e),
            r = t[0] * n - t[1] * i,
            o = t[1] * n + t[0] * i;
          return (t[0] = r), (t[1] = o), t;
        }
        function Xe(t, e) {
          if (e.canWrapX()) {
            var n = ke(e.getExtent()),
              i = (function (t, e, n) {
                var i = e.getExtent(),
                  r = 0;
                if (e.canWrapX() && (t[0] < i[0] || t[0] > i[2])) {
                  var o = n || ke(i);
                  r = Math.floor((t[0] - i[0]) / o);
                }
                return r;
              })(t, e, n);
            i && (t[0] -= i * n);
          }
          return t;
        }
        function Ne(t, e, n) {
          var i = n || 6371008.8,
            r = Et(t[1]),
            o = Et(e[1]),
            s = (o - r) / 2,
            a = Et(e[0] - t[0]) / 2,
            l =
              Math.sin(s) * Math.sin(s) +
              Math.sin(a) * Math.sin(a) * Math.cos(r) * Math.cos(o);
          return 2 * i * Math.atan2(Math.sqrt(l), Math.sqrt(1 - l));
        }
        var Ye = !0;
        function Be(t) {
          Ye = !(void 0 === t || t);
        }
        function Ke(t, e, n) {
          var i;
          if (void 0 !== e) {
            for (var r = 0, o = t.length; r < o; ++r) e[r] = t[r];
            i = e;
          } else i = t.slice();
          return i;
        }
        function Ze(t, e, n) {
          if (void 0 !== e && t !== e) {
            for (var i = 0, r = t.length; i < r; ++i) e[i] = t[i];
            t = e;
          }
          return t;
        }
        function Ve(t) {
          !(function (t, e) {
            se[t] = e;
          })(t.getCode(), t),
            le(t, t, Ke);
        }
        function Ue(t) {
          return "string" == typeof t
            ? se[(e = t)] ||
                se[
                  e.replace(/urn:(x-)?ogc:def:crs:EPSG:(.*:)?(\w+)$/, "EPSG:$3")
                ] ||
                null
            : t || null;
          var e;
        }
        function He(t, e, n, i) {
          var r,
            o = (t = Ue(t)).getPointResolutionFunc();
          if (o)
            (r = o(e, n)),
              i &&
                i !== t.getUnits() &&
                (a = t.getMetersPerUnit()) &&
                (r = (r * a) / Bt[i]);
          else {
            var s = t.getUnits();
            if ((s == Kt.DEGREES && !i) || i == Kt.DEGREES) r = e;
            else {
              var a,
                l = $e(t, Ue("EPSG:4326"));
              if (l === Ze && s !== Kt.DEGREES) r = e * t.getMetersPerUnit();
              else {
                var h = [
                  n[0] - e / 2,
                  n[1],
                  n[0] + e / 2,
                  n[1],
                  n[0],
                  n[1] - e / 2,
                  n[0],
                  n[1] + e / 2,
                ];
                r =
                  (Ne((h = l(h, h, 2)).slice(0, 2), h.slice(2, 4)) +
                    Ne(h.slice(4, 6), h.slice(6, 8))) /
                  2;
              }
              void 0 !== (a = i ? Bt[i] : t.getMetersPerUnit()) && (r /= a);
            }
          }
          return r;
        }
        function qe(t) {
          !(function (t) {
            t.forEach(Ve);
          })(t),
            t.forEach(function (e) {
              t.forEach(function (t) {
                e !== t && le(e, t, Ke);
              });
            });
        }
        function Je(t, e) {
          return t ? ("string" == typeof t ? Ue(t) : t) : Ue(e);
        }
        function Qe(t, e) {
          if (t === e) return !0;
          var n = t.getUnits() === e.getUnits();
          return (t.getCode() === e.getCode() || $e(t, e) === Ke) && n;
        }
        function $e(t, e) {
          var n = (function (t, e) {
            var n;
            return t in ae && e in ae[t] && (n = ae[t][e]), n;
          })(t.getCode(), e.getCode());
          return n || (n = Ze), n;
        }
        function tn(t, e) {
          return $e(Ue(t), Ue(e));
        }
        function en(t, e, n) {
          return tn(e, n)(t, void 0, t.length);
        }
        function nn(t, e, n, i) {
          return (function (t, e, n, i) {
            var r = [];
            if (i > 1)
              for (var o = t[2] - t[0], s = t[3] - t[1], a = 0; a < i; ++a)
                r.push(
                  t[0] + (o * a) / i,
                  t[1],
                  t[2],
                  t[1] + (s * a) / i,
                  t[2] - (o * a) / i,
                  t[3],
                  t[0],
                  t[3] - (s * a) / i
                );
            else r = [t[0], t[1], t[2], t[1], t[2], t[3], t[0], t[3]];
            e(r, r, 2);
            for (var l = [], h = [], u = ((a = 0), r.length); a < u; a += 2)
              l.push(r[a]), h.push(r[a + 1]);
            return (function (t, e, n) {
              return ve(
                Math.min.apply(null, t),
                Math.min.apply(null, e),
                Math.max.apply(null, t),
                Math.max.apply(null, e),
                n
              );
            })(l, h, n);
          })(t, tn(e, n), void 0, i);
        }
        var rn,
          on,
          sn,
          an = null;
        function ln() {
          return an;
        }
        function hn(t, e) {
          return t;
        }
        function un(t, e) {
          return (
            Ye &&
              !ze(t, [0, 0]) &&
              t[0] >= -180 &&
              t[0] <= 180 &&
              t[1] >= -90 &&
              t[1] <= 90 &&
              ((Ye = !1),
              console.warn(
                "Call useGeographic() from ol/proj once to work with [longitude, latitude] coordinates."
              )),
            t
          );
        }
        function cn(t, e) {
          return t;
        }
        function pn(t, e) {
          return t;
        }
        function fn(t, e) {
          return t;
        }
        qe(te),
          qe(oe),
          (rn = te),
          (on = function (t, e, n) {
            var i = t.length,
              r = n > 1 ? n : 2,
              o = e;
            void 0 === o && (o = r > 2 ? t.slice() : new Array(i));
            for (var s = 0; s < i; s += r) {
              o[s] = (Ht * t[s]) / 180;
              var a =
                Ut * Math.log(Math.tan((Math.PI * (+t[s + 1] + 90)) / 360));
              a > Qt ? (a = Qt) : a < -Qt && (a = -Qt), (o[s + 1] = a);
            }
            return o;
          }),
          (sn = function (t, e, n) {
            var i = t.length,
              r = n > 1 ? n : 2,
              o = e;
            void 0 === o && (o = r > 2 ? t.slice() : new Array(i));
            for (var s = 0; s < i; s += r)
              (o[s] = (180 * t[s]) / Ht),
                (o[s + 1] =
                  (360 * Math.atan(Math.exp(t[s + 1] / Ut))) / Math.PI - 90);
            return o;
          }),
          oe.forEach(function (t) {
            rn.forEach(function (e) {
              le(t, e, on), le(e, t, sn);
            });
          });
        var dn = (function () {
            var t = function (e, n) {
              return (
                (t =
                  Object.setPrototypeOf ||
                  ({ __proto__: [] } instanceof Array &&
                    function (t, e) {
                      t.__proto__ = e;
                    }) ||
                  function (t, e) {
                    for (var n in e)
                      Object.prototype.hasOwnProperty.call(e, n) &&
                        (t[n] = e[n]);
                  }),
                t(e, n)
              );
            };
            return function (e, n) {
              if ("function" != typeof n && null !== n)
                throw new TypeError(
                  "Class extends value " +
                    String(n) +
                    " is not a constructor or null"
                );
              function i() {
                this.constructor = e;
              }
              t(e, n),
                (e.prototype =
                  null === n
                    ? Object.create(n)
                    : ((i.prototype = n.prototype), new i()));
            };
          })(),
          gn = "projection",
          _n = "coordinateFormat",
          yn = (function (t) {
            function e(e) {
              var n = this,
                i = e || {},
                r = document.createElement("div");
              (r.className =
                void 0 !== i.className ? i.className : "ol-mouse-position"),
                (n =
                  t.call(this, {
                    element: r,
                    render: i.render,
                    target: i.target,
                  }) || this).on,
                n.once,
                n.un,
                n.addChangeListener(gn, n.handleProjectionChanged_),
                i.coordinateFormat && n.setCoordinateFormat(i.coordinateFormat),
                i.projection && n.setProjection(i.projection);
              var o = !0,
                s = "&#160;";
              return (
                "undefinedHTML" in i
                  ? (void 0 !== i.undefinedHTML && (s = i.undefinedHTML),
                    (o = !!s))
                  : "placeholder" in i &&
                    (!1 === i.placeholder
                      ? (o = !1)
                      : (s = String(i.placeholder))),
                (n.placeholder_ = s),
                (n.renderOnMouseOut_ = o),
                (n.renderedHTML_ = r.innerHTML),
                (n.mapProjection_ = null),
                (n.transform_ = null),
                n
              );
            }
            return (
              dn(e, t),
              (e.prototype.handleProjectionChanged_ = function () {
                this.transform_ = null;
              }),
              (e.prototype.getCoordinateFormat = function () {
                return this.get(_n);
              }),
              (e.prototype.getProjection = function () {
                return this.get(gn);
              }),
              (e.prototype.handleMouseMove = function (t) {
                var e = this.getMap();
                this.updateHTML_(e.getEventPixel(t));
              }),
              (e.prototype.handleMouseOut = function (t) {
                this.updateHTML_(null);
              }),
              (e.prototype.setMap = function (e) {
                if ((t.prototype.setMap.call(this, e), e)) {
                  var n = e.getViewport();
                  this.listenerKeys.push(O(n, Xt, this.handleMouseMove, this)),
                    this.renderOnMouseOut_ &&
                      this.listenerKeys.push(
                        O(n, "pointerout", this.handleMouseOut, this)
                      ),
                    this.updateHTML_(null);
                }
              }),
              (e.prototype.setCoordinateFormat = function (t) {
                this.set(_n, t);
              }),
              (e.prototype.setProjection = function (t) {
                this.set(gn, Ue(t));
              }),
              (e.prototype.updateHTML_ = function (t) {
                var e = this.placeholder_;
                if (t && this.mapProjection_) {
                  if (!this.transform_) {
                    var n = this.getProjection();
                    this.transform_ = n ? $e(this.mapProjection_, n) : Ze;
                  }
                  var i = this.getMap().getCoordinateFromPixelInternal(t);
                  if (i) {
                    var r = ln();
                    r && (this.transform_ = $e(this.mapProjection_, r)),
                      this.transform_(i, i);
                    var o = this.getCoordinateFormat();
                    e = o ? o(i) : i.toString();
                  }
                }
                (this.renderedHTML_ && e === this.renderedHTML_) ||
                  ((this.element.innerHTML = e), (this.renderedHTML_ = e));
              }),
              (e.prototype.render = function (t) {
                var e = t.frameState;
                e
                  ? this.mapProjection_ != e.viewState.projection &&
                    ((this.mapProjection_ = e.viewState.projection),
                    (this.transform_ = null))
                  : (this.mapProjection_ = null);
              }),
              e
            );
          })(tt),
          vn = yn;
        function mn(t) {
          return Math.pow(t, 3);
        }
        function xn(t) {
          return 1 - mn(1 - t);
        }
        function Cn(t) {
          return 3 * t * t - 2 * t * t * t;
        }
        function wn(t) {
          return t;
        }
        var Sn,
          En = (function () {
            var t = function (e, n) {
              return (
                (t =
                  Object.setPrototypeOf ||
                  ({ __proto__: [] } instanceof Array &&
                    function (t, e) {
                      t.__proto__ = e;
                    }) ||
                  function (t, e) {
                    for (var n in e)
                      Object.prototype.hasOwnProperty.call(e, n) &&
                        (t[n] = e[n]);
                  }),
                t(e, n)
              );
            };
            return function (e, n) {
              if ("function" != typeof n && null !== n)
                throw new TypeError(
                  "Class extends value " +
                    String(n) +
                    " is not a constructor or null"
                );
              function i() {
                this.constructor = e;
              }
              t(e, n),
                (e.prototype =
                  null === n
                    ? Object.create(n)
                    : ((i.prototype = n.prototype), new i()));
            };
          })(),
          Tn = (function (t) {
            function e(e) {
              var n = this,
                i = e || {};
              n =
                t.call(this, {
                  element: document.createElement("div"),
                  target: i.target,
                }) || this;
              var r = void 0 !== i.className ? i.className : "ol-zoom",
                o = void 0 !== i.delta ? i.delta : 1,
                s =
                  void 0 !== i.zoomInClassName ? i.zoomInClassName : r + "-in",
                a =
                  void 0 !== i.zoomOutClassName
                    ? i.zoomOutClassName
                    : r + "-out",
                l = void 0 !== i.zoomInLabel ? i.zoomInLabel : "+",
                h = void 0 !== i.zoomOutLabel ? i.zoomOutLabel : "–",
                u = void 0 !== i.zoomInTipLabel ? i.zoomInTipLabel : "Zoom in",
                c =
                  void 0 !== i.zoomOutTipLabel ? i.zoomOutTipLabel : "Zoom out",
                p = document.createElement("button");
              (p.className = s),
                p.setAttribute("type", "button"),
                (p.title = u),
                p.appendChild(
                  "string" == typeof l ? document.createTextNode(l) : l
                ),
                p.addEventListener(w, n.handleClick_.bind(n, o), !1);
              var f = document.createElement("button");
              (f.className = a),
                f.setAttribute("type", "button"),
                (f.title = c),
                f.appendChild(
                  "string" == typeof h ? document.createTextNode(h) : h
                ),
                f.addEventListener(w, n.handleClick_.bind(n, -o), !1);
              var d = r + " ol-unselectable " + nt,
                g = n.element;
              return (
                (g.className = d),
                g.appendChild(p),
                g.appendChild(f),
                (n.duration_ = void 0 !== i.duration ? i.duration : 250),
                n
              );
            }
            return (
              En(e, t),
              (e.prototype.handleClick_ = function (t, e) {
                e.preventDefault(), this.zoomByDelta_(t);
              }),
              (e.prototype.zoomByDelta_ = function (t) {
                var e = this.getMap().getView();
                if (e) {
                  var n = e.getZoom();
                  if (void 0 !== n) {
                    var i = e.getConstrainedZoom(n + t);
                    this.duration_ > 0
                      ? (e.getAnimating() && e.cancelAnimations(),
                        e.animate({
                          zoom: i,
                          duration: this.duration_,
                          easing: xn,
                        }))
                      : e.setZoom(i);
                  }
                }
              }),
              e
            );
          })(tt),
          bn = "XY",
          On = "XYM",
          Rn = "XYZM",
          In = "Point",
          Pn = "LineString",
          Mn = "Polygon",
          Fn = "MultiPoint",
          Ln = "MultiLineString",
          An = "MultiPolygon",
          Dn = "GeometryCollection",
          kn = "Circle";
        function jn(t, e) {
          var n = e[0],
            i = e[1];
          return (
            (e[0] = t[0] * n + t[2] * i + t[4]),
            (e[1] = t[1] * n + t[3] * i + t[5]),
            e
          );
        }
        function Gn(t, e, n, i, r, o, s, a) {
          var l = Math.sin(o),
            h = Math.cos(o);
          return (
            (t[0] = i * h),
            (t[1] = r * l),
            (t[2] = -i * l),
            (t[3] = r * h),
            (t[4] = s * i * h - a * i * l + e),
            (t[5] = s * r * l + a * r * h + n),
            t
          );
        }
        function zn(t, e) {
          var n,
            i = (n = e)[0] * n[3] - n[1] * n[2];
          vt(0 !== i, 32);
          var r = e[0],
            o = e[1],
            s = e[2],
            a = e[3],
            l = e[4],
            h = e[5];
          return (
            (t[0] = a / i),
            (t[1] = -o / i),
            (t[2] = -s / i),
            (t[3] = r / i),
            (t[4] = (s * h - a * l) / i),
            (t[5] = -(r * h - o * l) / i),
            t
          );
        }
        function Wn(t) {
          var e = "matrix(" + t.join(", ") + ")";
          if (V) return e;
          var n = Sn || (Sn = document.createElement("div"));
          return (n.style.transform = e), n.style.transform;
        }
        function Xn(t, e, n, i, r, o) {
          for (var s = o || [], a = 0, l = e; l < n; l += i) {
            var h = t[l],
              u = t[l + 1];
            (s[a++] = r[0] * h + r[2] * u + r[4]),
              (s[a++] = r[1] * h + r[3] * u + r[5]);
          }
          return o && s.length != a && (s.length = a), s;
        }
        function Nn(t, e, n, i, r, o, s) {
          for (
            var a = s || [],
              l = Math.cos(r),
              h = Math.sin(r),
              u = o[0],
              c = o[1],
              p = 0,
              f = e;
            f < n;
            f += i
          ) {
            var d = t[f] - u,
              g = t[f + 1] - c;
            (a[p++] = u + d * l - g * h), (a[p++] = c + d * h + g * l);
            for (var _ = f + 2; _ < f + i; ++_) a[p++] = t[_];
          }
          return s && a.length != p && (a.length = p), a;
        }
        new Array(6);
        var Yn = (function () {
            var t = function (e, n) {
              return (
                (t =
                  Object.setPrototypeOf ||
                  ({ __proto__: [] } instanceof Array &&
                    function (t, e) {
                      t.__proto__ = e;
                    }) ||
                  function (t, e) {
                    for (var n in e)
                      Object.prototype.hasOwnProperty.call(e, n) &&
                        (t[n] = e[n]);
                  }),
                t(e, n)
              );
            };
            return function (e, n) {
              if ("function" != typeof n && null !== n)
                throw new TypeError(
                  "Class extends value " +
                    String(n) +
                    " is not a constructor or null"
                );
              function i() {
                this.constructor = e;
              }
              t(e, n),
                (e.prototype =
                  null === n
                    ? Object.create(n)
                    : ((i.prototype = n.prototype), new i()));
            };
          })(),
          Bn = [1, 0, 0, 1, 0, 0],
          Kn = (function (t) {
            function e() {
              var e,
                n,
                i,
                r,
                o,
                s = t.call(this) || this;
              return (
                (s.extent_ = [1 / 0, 1 / 0, -1 / 0, -1 / 0]),
                (s.extentRevision_ = -1),
                (s.simplifiedGeometryMaxMinSquaredTolerance = 0),
                (s.simplifiedGeometryRevision = 0),
                (s.simplifyTransformedInternal =
                  ((e = function (t, e, n) {
                    if (!n) return this.getSimplifiedGeometry(e);
                    var i = this.clone();
                    return i.applyTransform(n), i.getSimplifiedGeometry(e);
                  }),
                  (o = !1),
                  function () {
                    var t = Array.prototype.slice.call(arguments);
                    return (
                      (o && this === r && h(t, i)) ||
                        ((o = !0),
                        (r = this),
                        (i = t),
                        (n = e.apply(this, arguments))),
                      n
                    );
                  })),
                s
              );
            }
            return (
              Yn(e, t),
              (e.prototype.simplifyTransformed = function (t, e) {
                return this.simplifyTransformedInternal(
                  this.getRevision(),
                  t,
                  e
                );
              }),
              (e.prototype.clone = function () {
                return L();
              }),
              (e.prototype.closestPointXY = function (t, e, n, i) {
                return L();
              }),
              (e.prototype.containsXY = function (t, e) {
                var n = this.getClosestPoint([t, e]);
                return n[0] === t && n[1] === e;
              }),
              (e.prototype.getClosestPoint = function (t, e) {
                var n = e || [NaN, NaN];
                return this.closestPointXY(t[0], t[1], n, 1 / 0), n;
              }),
              (e.prototype.intersectsCoordinate = function (t) {
                return this.containsXY(t[0], t[1]);
              }),
              (e.prototype.computeExtent = function (t) {
                return L();
              }),
              (e.prototype.getExtent = function (t) {
                if (this.extentRevision_ != this.getRevision()) {
                  var e = this.computeExtent(this.extent_);
                  (isNaN(e[0]) || isNaN(e[1])) && me(e),
                    (this.extentRevision_ = this.getRevision());
                }
                return (function (t, e) {
                  return e
                    ? ((e[0] = t[0]),
                      (e[1] = t[1]),
                      (e[2] = t[2]),
                      (e[3] = t[3]),
                      e)
                    : t;
                })(this.extent_, t);
              }),
              (e.prototype.rotate = function (t, e) {
                L();
              }),
              (e.prototype.scale = function (t, e, n) {
                L();
              }),
              (e.prototype.simplify = function (t) {
                return this.getSimplifiedGeometry(t * t);
              }),
              (e.prototype.getSimplifiedGeometry = function (t) {
                return L();
              }),
              (e.prototype.getType = function () {
                return L();
              }),
              (e.prototype.applyTransform = function (t) {
                L();
              }),
              (e.prototype.intersectsExtent = function (t) {
                return L();
              }),
              (e.prototype.translate = function (t, e) {
                L();
              }),
              (e.prototype.transform = function (t, e) {
                var n = Ue(t),
                  i =
                    n.getUnits() == Kt.TILE_PIXELS
                      ? function (t, i, r) {
                          var o = n.getExtent(),
                            s = n.getWorldExtent(),
                            a = Fe(s) / Fe(o);
                          return (
                            Gn(Bn, s[0], s[3], a, -a, 0, 0, 0),
                            Xn(t, 0, t.length, r, Bn, i),
                            tn(n, e)(t, i, r)
                          );
                        }
                      : tn(n, e);
                return this.applyTransform(i), this;
              }),
              e
            );
          })(G),
          Zn = Kn,
          Vn = (function () {
            var t = function (e, n) {
              return (
                (t =
                  Object.setPrototypeOf ||
                  ({ __proto__: [] } instanceof Array &&
                    function (t, e) {
                      t.__proto__ = e;
                    }) ||
                  function (t, e) {
                    for (var n in e)
                      Object.prototype.hasOwnProperty.call(e, n) &&
                        (t[n] = e[n]);
                  }),
                t(e, n)
              );
            };
            return function (e, n) {
              if ("function" != typeof n && null !== n)
                throw new TypeError(
                  "Class extends value " +
                    String(n) +
                    " is not a constructor or null"
                );
              function i() {
                this.constructor = e;
              }
              t(e, n),
                (e.prototype =
                  null === n
                    ? Object.create(n)
                    : ((i.prototype = n.prototype), new i()));
            };
          })(),
          Un = (function (t) {
            function e() {
              var e = t.call(this) || this;
              return (
                (e.layout = bn), (e.stride = 2), (e.flatCoordinates = null), e
              );
            }
            return (
              Vn(e, t),
              (e.prototype.computeExtent = function (t) {
                return xe(
                  this.flatCoordinates,
                  0,
                  this.flatCoordinates.length,
                  this.stride,
                  t
                );
              }),
              (e.prototype.getCoordinates = function () {
                return L();
              }),
              (e.prototype.getFirstCoordinate = function () {
                return this.flatCoordinates.slice(0, this.stride);
              }),
              (e.prototype.getFlatCoordinates = function () {
                return this.flatCoordinates;
              }),
              (e.prototype.getLastCoordinate = function () {
                return this.flatCoordinates.slice(
                  this.flatCoordinates.length - this.stride
                );
              }),
              (e.prototype.getLayout = function () {
                return this.layout;
              }),
              (e.prototype.getSimplifiedGeometry = function (t) {
                if (
                  (this.simplifiedGeometryRevision !== this.getRevision() &&
                    ((this.simplifiedGeometryMaxMinSquaredTolerance = 0),
                    (this.simplifiedGeometryRevision = this.getRevision())),
                  t < 0 ||
                    (0 !== this.simplifiedGeometryMaxMinSquaredTolerance &&
                      t <= this.simplifiedGeometryMaxMinSquaredTolerance))
                )
                  return this;
                var e = this.getSimplifiedGeometryInternal(t);
                return e.getFlatCoordinates().length <
                  this.flatCoordinates.length
                  ? e
                  : ((this.simplifiedGeometryMaxMinSquaredTolerance = t), this);
              }),
              (e.prototype.getSimplifiedGeometryInternal = function (t) {
                return this;
              }),
              (e.prototype.getStride = function () {
                return this.stride;
              }),
              (e.prototype.setFlatCoordinates = function (t, e) {
                (this.stride = Hn(t)),
                  (this.layout = t),
                  (this.flatCoordinates = e);
              }),
              (e.prototype.setCoordinates = function (t, e) {
                L();
              }),
              (e.prototype.setLayout = function (t, e, n) {
                var i;
                if (t) i = Hn(t);
                else {
                  for (var r = 0; r < n; ++r) {
                    if (0 === e.length)
                      return (this.layout = bn), void (this.stride = 2);
                    e = e[0];
                  }
                  t = (function (t) {
                    var e;
                    return (
                      2 == t
                        ? (e = bn)
                        : 3 == t
                        ? (e = "XYZ")
                        : 4 == t && (e = Rn),
                      e
                    );
                  })((i = e.length));
                }
                (this.layout = t), (this.stride = i);
              }),
              (e.prototype.applyTransform = function (t) {
                this.flatCoordinates &&
                  (t(this.flatCoordinates, this.flatCoordinates, this.stride),
                  this.changed());
              }),
              (e.prototype.rotate = function (t, e) {
                var n = this.getFlatCoordinates();
                if (n) {
                  var i = this.getStride();
                  Nn(n, 0, n.length, i, t, e, n), this.changed();
                }
              }),
              (e.prototype.scale = function (t, e, n) {
                var i = e;
                void 0 === i && (i = t);
                var r = n;
                r || (r = Ie(this.getExtent()));
                var o = this.getFlatCoordinates();
                if (o) {
                  var s = this.getStride();
                  !(function (t, e, n, i, r, o, s, a) {
                    for (
                      var l = a || [], h = s[0], u = s[1], c = 0, p = 0;
                      p < n;
                      p += i
                    ) {
                      var f = t[p] - h,
                        d = t[p + 1] - u;
                      (l[c++] = h + r * f), (l[c++] = u + o * d);
                      for (var g = p + 2; g < p + i; ++g) l[c++] = t[g];
                    }
                    a && l.length != c && (l.length = c);
                  })(o, 0, o.length, s, t, i, r, o),
                    this.changed();
                }
              }),
              (e.prototype.translate = function (t, e) {
                var n = this.getFlatCoordinates();
                if (n) {
                  var i = this.getStride();
                  !(function (t, e, n, i, r, o, s) {
                    for (var a = s || [], l = 0, h = 0; h < n; h += i) {
                      (a[l++] = t[h] + r), (a[l++] = t[h + 1] + o);
                      for (var u = h + 2; u < h + i; ++u) a[l++] = t[u];
                    }
                    s && a.length != l && (a.length = l);
                  })(n, 0, n.length, i, t, e, n),
                    this.changed();
                }
              }),
              e
            );
          })(Zn);
        function Hn(t) {
          var e;
          return (
            t == bn
              ? (e = 2)
              : "XYZ" == t || t == On
              ? (e = 3)
              : t == Rn && (e = 4),
            e
          );
        }
        var qn = Un;
        function Jn(t, e, n, i, r, o, s) {
          var a,
            l = t[e],
            h = t[e + 1],
            u = t[n] - l,
            c = t[n + 1] - h;
          if (0 === u && 0 === c) a = e;
          else {
            var p = ((r - l) * u + (o - h) * c) / (u * u + c * c);
            if (p > 1) a = n;
            else {
              if (p > 0) {
                for (var f = 0; f < i; ++f) s[f] = bt(t[e + f], t[n + f], p);
                return void (s.length = i);
              }
              a = e;
            }
          }
          for (f = 0; f < i; ++f) s[f] = t[a + f];
          s.length = i;
        }
        function Qn(t, e, n, i, r) {
          var o = t[e],
            s = t[e + 1];
          for (e += i; e < n; e += i) {
            var a = t[e],
              l = t[e + 1],
              h = St(o, s, a, l);
            h > r && (r = h), (o = a), (s = l);
          }
          return r;
        }
        function $n(t, e, n, i, r) {
          for (var o = 0, s = n.length; o < s; ++o) {
            var a = n[o];
            (r = Qn(t, e, a, i, r)), (e = a);
          }
          return r;
        }
        function ti(t, e, n, i, r, o, s, a, l, h, u) {
          if (e == n) return h;
          var c, p;
          if (0 === r) {
            if ((p = St(s, a, t[e], t[e + 1])) < h) {
              for (c = 0; c < i; ++c) l[c] = t[e + c];
              return (l.length = i), p;
            }
            return h;
          }
          for (var f = u || [NaN, NaN], d = e + i; d < n; )
            if ((Jn(t, d - i, d, i, s, a, f), (p = St(s, a, f[0], f[1])) < h)) {
              for (h = p, c = 0; c < i; ++c) l[c] = f[c];
              (l.length = i), (d += i);
            } else
              d += i * Math.max(((Math.sqrt(p) - Math.sqrt(h)) / r) | 0, 1);
          if (
            o &&
            (Jn(t, n - i, e, i, s, a, f), (p = St(s, a, f[0], f[1])) < h)
          ) {
            for (h = p, c = 0; c < i; ++c) l[c] = f[c];
            l.length = i;
          }
          return h;
        }
        function ei(t, e, n, i, r, o, s, a, l, h, u) {
          for (var c = u || [NaN, NaN], p = 0, f = n.length; p < f; ++p) {
            var d = n[p];
            (h = ti(t, e, d, i, r, o, s, a, l, h, c)), (e = d);
          }
          return h;
        }
        function ni(t, e, n, i) {
          for (var r = 0, o = n.length; r < o; ++r)
            for (var s = n[r], a = 0; a < i; ++a) t[e++] = s[a];
          return e;
        }
        function ii(t, e, n, i, r) {
          for (var o = r || [], s = 0, a = 0, l = n.length; a < l; ++a) {
            var h = ni(t, e, n[a], i);
            (o[s++] = h), (e = h);
          }
          return (o.length = s), o;
        }
        function ri(t, e, n, i, r, o, s) {
          var a = (n - e) / i;
          if (a < 3) {
            for (; e < n; e += i) (o[s++] = t[e]), (o[s++] = t[e + 1]);
            return s;
          }
          var l = new Array(a);
          (l[0] = 1), (l[a - 1] = 1);
          for (var h = [e, n - i], u = 0; h.length > 0; ) {
            for (
              var c = h.pop(),
                p = h.pop(),
                f = 0,
                d = t[p],
                g = t[p + 1],
                _ = t[c],
                y = t[c + 1],
                v = p + i;
              v < c;
              v += i
            ) {
              var m = wt(t[v], t[v + 1], d, g, _, y);
              m > f && ((u = v), (f = m));
            }
            f > r &&
              ((l[(u - e) / i] = 1),
              p + i < u && h.push(p, u),
              u + i < c && h.push(u, c));
          }
          for (v = 0; v < a; ++v)
            l[v] && ((o[s++] = t[e + v * i]), (o[s++] = t[e + v * i + 1]));
          return s;
        }
        function oi(t, e) {
          return e * Math.round(t / e);
        }
        function si(t, e, n, i, r, o, s) {
          if (e == n) return s;
          var a,
            l,
            h = oi(t[e], r),
            u = oi(t[e + 1], r);
          (e += i), (o[s++] = h), (o[s++] = u);
          do {
            if (((a = oi(t[e], r)), (l = oi(t[e + 1], r)), (e += i) == n))
              return (o[s++] = a), (o[s++] = l), s;
          } while (a == h && l == u);
          for (; e < n; ) {
            var c = oi(t[e], r),
              p = oi(t[e + 1], r);
            if (((e += i), c != a || p != l)) {
              var f = a - h,
                d = l - u,
                g = c - h,
                _ = p - u;
              f * _ == d * g &&
              ((f < 0 && g < f) || f == g || (f > 0 && g > f)) &&
              ((d < 0 && _ < d) || d == _ || (d > 0 && _ > d))
                ? ((a = c), (l = p))
                : ((o[s++] = a),
                  (o[s++] = l),
                  (h = a),
                  (u = l),
                  (a = c),
                  (l = p));
            }
          }
          return (o[s++] = a), (o[s++] = l), s;
        }
        function ai(t, e, n, i, r, o, s, a) {
          for (var l = 0, h = n.length; l < h; ++l) {
            var u = n[l];
            (s = si(t, e, u, i, r, o, s)), a.push(s), (e = u);
          }
          return s;
        }
        function li(t, e, n, i, r) {
          var o;
          for (e += i; e < n; e += i)
            if ((o = r(t.slice(e - i, e), t.slice(e, e + i)))) return o;
          return !1;
        }
        function hi(t, e, n, i, r) {
          for (var o = void 0 !== r ? r : [], s = 0, a = e; a < n; a += i)
            o[s++] = t.slice(a, a + i);
          return (o.length = s), o;
        }
        function ui(t, e, n, i, r) {
          for (
            var o = void 0 !== r ? r : [], s = 0, a = 0, l = n.length;
            a < l;
            ++a
          ) {
            var h = n[a];
            (o[s++] = hi(t, e, h, i, o[s])), (e = h);
          }
          return (o.length = s), o;
        }
        function ci(t, e, n, i, r) {
          for (
            var o = void 0 !== r ? r : [], s = 0, a = 0, l = n.length;
            a < l;
            ++a
          ) {
            var h = n[a];
            (o[s++] = ui(t, e, h, i, o[s])), (e = h[h.length - 1]);
          }
          return (o.length = s), o;
        }
        function pi(t, e, n, i, r, s, a) {
          var l,
            h,
            u = (n - e) / i;
          if (1 === u) l = e;
          else if (2 === u) (l = e), (h = r);
          else if (0 !== u) {
            for (
              var c = t[e], p = t[e + 1], f = 0, d = [0], g = e + i;
              g < n;
              g += i
            ) {
              var _ = t[g],
                y = t[g + 1];
              (f += Math.sqrt((_ - c) * (_ - c) + (y - p) * (y - p))),
                d.push(f),
                (c = _),
                (p = y);
            }
            var v = r * f,
              m = (function (t, e, n) {
                for (var i, r, s = o, a = 0, l = t.length, h = !1; a < l; )
                  (r = +s(t[(i = a + ((l - a) >> 1))], e)) < 0
                    ? (a = i + 1)
                    : ((l = i), (h = !r));
                return h ? a : ~a;
              })(d, v);
            m < 0
              ? ((h = (v - d[-m - 2]) / (d[-m - 1] - d[-m - 2])),
                (l = e + (-m - 2) * i))
              : (l = e + m * i);
          }
          var x = a > 1 ? a : 2,
            C = s || new Array(x);
          for (g = 0; g < x; ++g)
            C[g] =
              void 0 === l
                ? NaN
                : void 0 === h
                ? t[l + g]
                : bt(t[l + g], t[l + i + g], h);
          return C;
        }
        function fi(t, e, n, i, r, o) {
          if (n == e) return null;
          var s;
          if (r < t[e + i - 1])
            return o ? (((s = t.slice(e, e + i))[i - 1] = r), s) : null;
          if (t[n - 1] < r)
            return o ? (((s = t.slice(n - i, n))[i - 1] = r), s) : null;
          if (r == t[e + i - 1]) return t.slice(e, e + i);
          for (var a = e / i, l = n / i; a < l; ) {
            var h = (a + l) >> 1;
            r < t[(h + 1) * i - 1] ? (l = h) : (a = h + 1);
          }
          var u = t[a * i - 1];
          if (r == u) return t.slice((a - 1) * i, (a - 1) * i + i);
          var c = (r - u) / (t[(a + 1) * i - 1] - u);
          s = [];
          for (var p = 0; p < i - 1; ++p)
            s.push(bt(t[(a - 1) * i + p], t[a * i + p], c));
          return s.push(r), s;
        }
        function di(t, e, n, i, r) {
          return !Te(r, function (r) {
            return !gi(t, e, n, i, r[0], r[1]);
          });
        }
        function gi(t, e, n, i, r, o) {
          for (var s = 0, a = t[n - i], l = t[n - i + 1]; e < n; e += i) {
            var h = t[e],
              u = t[e + 1];
            l <= o
              ? u > o && (h - a) * (o - l) - (r - a) * (u - l) > 0 && s++
              : u <= o && (h - a) * (o - l) - (r - a) * (u - l) < 0 && s--,
              (a = h),
              (l = u);
          }
          return 0 !== s;
        }
        function _i(t, e, n, i, r, o) {
          if (0 === n.length) return !1;
          if (!gi(t, e, n[0], i, r, o)) return !1;
          for (var s = 1, a = n.length; s < a; ++s)
            if (gi(t, n[s - 1], n[s], i, r, o)) return !1;
          return !0;
        }
        function yi(t, e, n, i, r) {
          var o = Se([1 / 0, 1 / 0, -1 / 0, -1 / 0], t, e, n, i);
          return (
            !!je(r, o) &&
            (!!ge(r, o) ||
              (o[0] >= r[0] && o[2] <= r[2]) ||
              (o[1] >= r[1] && o[3] <= r[3]) ||
              li(t, e, n, i, function (t, e) {
                return (function (t, e, n) {
                  var i = !1,
                    r = ye(t, e),
                    o = ye(t, n);
                  if (1 === r || 1 === o) i = !0;
                  else {
                    var s = t[0],
                      a = t[1],
                      l = t[2],
                      h = t[3],
                      u = e[0],
                      c = e[1],
                      p = n[0],
                      f = n[1],
                      d = (f - c) / (p - u),
                      g = void 0,
                      _ = void 0;
                    2 & o &&
                      !(2 & r) &&
                      (i = (g = p - (f - h) / d) >= s && g <= l),
                      i ||
                        !(4 & o) ||
                        4 & r ||
                        (i = (_ = f - (p - l) * d) >= a && _ <= h),
                      i ||
                        !(8 & o) ||
                        8 & r ||
                        (i = (g = p - (f - a) / d) >= s && g <= l),
                      i ||
                        !(16 & o) ||
                        16 & r ||
                        (i = (_ = f - (p - s) * d) >= a && _ <= h);
                  }
                  return i;
                })(r, t, e);
              }))
          );
        }
        function vi(t, e, n, i, r) {
          if (
            !(function (t, e, n, i, r) {
              return !!(
                yi(t, e, n, i, r) ||
                gi(t, e, n, i, r[0], r[1]) ||
                gi(t, e, n, i, r[0], r[3]) ||
                gi(t, e, n, i, r[2], r[1]) ||
                gi(t, e, n, i, r[2], r[3])
              );
            })(t, e, n[0], i, r)
          )
            return !1;
          if (1 === n.length) return !0;
          for (var o = 1, s = n.length; o < s; ++o)
            if (di(t, n[o - 1], n[o], i, r) && !yi(t, n[o - 1], n[o], i, r))
              return !1;
          return !0;
        }
        function mi(t, e, n, i) {
          for (var r = t[e], o = t[e + 1], s = 0, a = e + i; a < n; a += i) {
            var l = t[a],
              h = t[a + 1];
            (s += Math.sqrt((l - r) * (l - r) + (h - o) * (h - o))),
              (r = l),
              (o = h);
          }
          return s;
        }
        var xi = (function () {
            var t = function (e, n) {
              return (
                (t =
                  Object.setPrototypeOf ||
                  ({ __proto__: [] } instanceof Array &&
                    function (t, e) {
                      t.__proto__ = e;
                    }) ||
                  function (t, e) {
                    for (var n in e)
                      Object.prototype.hasOwnProperty.call(e, n) &&
                        (t[n] = e[n]);
                  }),
                t(e, n)
              );
            };
            return function (e, n) {
              if ("function" != typeof n && null !== n)
                throw new TypeError(
                  "Class extends value " +
                    String(n) +
                    " is not a constructor or null"
                );
              function i() {
                this.constructor = e;
              }
              t(e, n),
                (e.prototype =
                  null === n
                    ? Object.create(n)
                    : ((i.prototype = n.prototype), new i()));
            };
          })(),
          Ci = (function (t) {
            function e(e, n) {
              var i = t.call(this) || this;
              return (
                (i.flatMidpoint_ = null),
                (i.flatMidpointRevision_ = -1),
                (i.maxDelta_ = -1),
                (i.maxDeltaRevision_ = -1),
                void 0 === n || Array.isArray(e[0])
                  ? i.setCoordinates(e, n)
                  : i.setFlatCoordinates(n, e),
                i
              );
            }
            return (
              xi(e, t),
              (e.prototype.appendCoordinate = function (t) {
                this.flatCoordinates
                  ? l(this.flatCoordinates, t)
                  : (this.flatCoordinates = t.slice()),
                  this.changed();
              }),
              (e.prototype.clone = function () {
                var t = new e(this.flatCoordinates.slice(), this.layout);
                return t.applyProperties(this), t;
              }),
              (e.prototype.closestPointXY = function (t, e, n, i) {
                return i < fe(this.getExtent(), t, e)
                  ? i
                  : (this.maxDeltaRevision_ != this.getRevision() &&
                      ((this.maxDelta_ = Math.sqrt(
                        Qn(
                          this.flatCoordinates,
                          0,
                          this.flatCoordinates.length,
                          this.stride,
                          0
                        )
                      )),
                      (this.maxDeltaRevision_ = this.getRevision())),
                    ti(
                      this.flatCoordinates,
                      0,
                      this.flatCoordinates.length,
                      this.stride,
                      this.maxDelta_,
                      !1,
                      t,
                      e,
                      n,
                      i
                    ));
              }),
              (e.prototype.forEachSegment = function (t) {
                return li(
                  this.flatCoordinates,
                  0,
                  this.flatCoordinates.length,
                  this.stride,
                  t
                );
              }),
              (e.prototype.getCoordinateAtM = function (t, e) {
                if (this.layout != On && this.layout != Rn) return null;
                var n = void 0 !== e && e;
                return fi(
                  this.flatCoordinates,
                  0,
                  this.flatCoordinates.length,
                  this.stride,
                  t,
                  n
                );
              }),
              (e.prototype.getCoordinates = function () {
                return hi(
                  this.flatCoordinates,
                  0,
                  this.flatCoordinates.length,
                  this.stride
                );
              }),
              (e.prototype.getCoordinateAt = function (t, e) {
                return pi(
                  this.flatCoordinates,
                  0,
                  this.flatCoordinates.length,
                  this.stride,
                  t,
                  e,
                  this.stride
                );
              }),
              (e.prototype.getLength = function () {
                return mi(
                  this.flatCoordinates,
                  0,
                  this.flatCoordinates.length,
                  this.stride
                );
              }),
              (e.prototype.getFlatMidpoint = function () {
                return (
                  this.flatMidpointRevision_ != this.getRevision() &&
                    ((this.flatMidpoint_ = this.getCoordinateAt(
                      0.5,
                      this.flatMidpoint_
                    )),
                    (this.flatMidpointRevision_ = this.getRevision())),
                  this.flatMidpoint_
                );
              }),
              (e.prototype.getSimplifiedGeometryInternal = function (t) {
                var n = [];
                return (
                  (n.length = ri(
                    this.flatCoordinates,
                    0,
                    this.flatCoordinates.length,
                    this.stride,
                    t,
                    n,
                    0
                  )),
                  new e(n, bn)
                );
              }),
              (e.prototype.getType = function () {
                return Pn;
              }),
              (e.prototype.intersectsExtent = function (t) {
                return yi(
                  this.flatCoordinates,
                  0,
                  this.flatCoordinates.length,
                  this.stride,
                  t
                );
              }),
              (e.prototype.setCoordinates = function (t, e) {
                this.setLayout(e, t, 1),
                  this.flatCoordinates || (this.flatCoordinates = []),
                  (this.flatCoordinates.length = ni(
                    this.flatCoordinates,
                    0,
                    t,
                    this.stride
                  )),
                  this.changed();
              }),
              e
            );
          })(qn),
          wi = Ci;
        function Si(t, e, n, i) {
          for (var r = 0, o = t[n - i], s = t[n - i + 1]; e < n; e += i) {
            var a = t[e],
              l = t[e + 1];
            (r += s * a - o * l), (o = a), (s = l);
          }
          return r / 2;
        }
        function Ei(t, e, n, i) {
          for (var r = 0, o = 0, s = n.length; o < s; ++o) {
            var a = n[o];
            (r += Si(t, e, a, i)), (e = a);
          }
          return r;
        }
        var Ti = (function () {
            var t = function (e, n) {
              return (
                (t =
                  Object.setPrototypeOf ||
                  ({ __proto__: [] } instanceof Array &&
                    function (t, e) {
                      t.__proto__ = e;
                    }) ||
                  function (t, e) {
                    for (var n in e)
                      Object.prototype.hasOwnProperty.call(e, n) &&
                        (t[n] = e[n]);
                  }),
                t(e, n)
              );
            };
            return function (e, n) {
              if ("function" != typeof n && null !== n)
                throw new TypeError(
                  "Class extends value " +
                    String(n) +
                    " is not a constructor or null"
                );
              function i() {
                this.constructor = e;
              }
              t(e, n),
                (e.prototype =
                  null === n
                    ? Object.create(n)
                    : ((i.prototype = n.prototype), new i()));
            };
          })(),
          bi = (function (t) {
            function e(e, n) {
              var i = t.call(this) || this;
              return (
                (i.maxDelta_ = -1),
                (i.maxDeltaRevision_ = -1),
                void 0 === n || Array.isArray(e[0])
                  ? i.setCoordinates(e, n)
                  : i.setFlatCoordinates(n, e),
                i
              );
            }
            return (
              Ti(e, t),
              (e.prototype.clone = function () {
                return new e(this.flatCoordinates.slice(), this.layout);
              }),
              (e.prototype.closestPointXY = function (t, e, n, i) {
                return i < fe(this.getExtent(), t, e)
                  ? i
                  : (this.maxDeltaRevision_ != this.getRevision() &&
                      ((this.maxDelta_ = Math.sqrt(
                        Qn(
                          this.flatCoordinates,
                          0,
                          this.flatCoordinates.length,
                          this.stride,
                          0
                        )
                      )),
                      (this.maxDeltaRevision_ = this.getRevision())),
                    ti(
                      this.flatCoordinates,
                      0,
                      this.flatCoordinates.length,
                      this.stride,
                      this.maxDelta_,
                      !0,
                      t,
                      e,
                      n,
                      i
                    ));
              }),
              (e.prototype.getArea = function () {
                return Si(
                  this.flatCoordinates,
                  0,
                  this.flatCoordinates.length,
                  this.stride
                );
              }),
              (e.prototype.getCoordinates = function () {
                return hi(
                  this.flatCoordinates,
                  0,
                  this.flatCoordinates.length,
                  this.stride
                );
              }),
              (e.prototype.getSimplifiedGeometryInternal = function (t) {
                var n = [];
                return (
                  (n.length = ri(
                    this.flatCoordinates,
                    0,
                    this.flatCoordinates.length,
                    this.stride,
                    t,
                    n,
                    0
                  )),
                  new e(n, bn)
                );
              }),
              (e.prototype.getType = function () {
                return "LinearRing";
              }),
              (e.prototype.intersectsExtent = function (t) {
                return !1;
              }),
              (e.prototype.setCoordinates = function (t, e) {
                this.setLayout(e, t, 1),
                  this.flatCoordinates || (this.flatCoordinates = []),
                  (this.flatCoordinates.length = ni(
                    this.flatCoordinates,
                    0,
                    t,
                    this.stride
                  )),
                  this.changed();
              }),
              e
            );
          })(qn),
          Oi = bi,
          Ri = (function () {
            var t = function (e, n) {
              return (
                (t =
                  Object.setPrototypeOf ||
                  ({ __proto__: [] } instanceof Array &&
                    function (t, e) {
                      t.__proto__ = e;
                    }) ||
                  function (t, e) {
                    for (var n in e)
                      Object.prototype.hasOwnProperty.call(e, n) &&
                        (t[n] = e[n]);
                  }),
                t(e, n)
              );
            };
            return function (e, n) {
              if ("function" != typeof n && null !== n)
                throw new TypeError(
                  "Class extends value " +
                    String(n) +
                    " is not a constructor or null"
                );
              function i() {
                this.constructor = e;
              }
              t(e, n),
                (e.prototype =
                  null === n
                    ? Object.create(n)
                    : ((i.prototype = n.prototype), new i()));
            };
          })(),
          Ii = (function (t) {
            function e(e, n, i) {
              var r = t.call(this) || this;
              if (
                ((r.ends_ = []),
                (r.maxDelta_ = -1),
                (r.maxDeltaRevision_ = -1),
                Array.isArray(e[0]))
              )
                r.setCoordinates(e, n);
              else if (void 0 !== n && i)
                r.setFlatCoordinates(n, e), (r.ends_ = i);
              else {
                for (
                  var o = r.getLayout(),
                    s = e,
                    a = [],
                    h = [],
                    u = 0,
                    c = s.length;
                  u < c;
                  ++u
                ) {
                  var p = s[u];
                  0 === u && (o = p.getLayout()),
                    l(a, p.getFlatCoordinates()),
                    h.push(a.length);
                }
                r.setFlatCoordinates(o, a), (r.ends_ = h);
              }
              return r;
            }
            return (
              Ri(e, t),
              (e.prototype.appendLineString = function (t) {
                this.flatCoordinates
                  ? l(this.flatCoordinates, t.getFlatCoordinates().slice())
                  : (this.flatCoordinates = t.getFlatCoordinates().slice()),
                  this.ends_.push(this.flatCoordinates.length),
                  this.changed();
              }),
              (e.prototype.clone = function () {
                var t = new e(
                  this.flatCoordinates.slice(),
                  this.layout,
                  this.ends_.slice()
                );
                return t.applyProperties(this), t;
              }),
              (e.prototype.closestPointXY = function (t, e, n, i) {
                return i < fe(this.getExtent(), t, e)
                  ? i
                  : (this.maxDeltaRevision_ != this.getRevision() &&
                      ((this.maxDelta_ = Math.sqrt(
                        $n(this.flatCoordinates, 0, this.ends_, this.stride, 0)
                      )),
                      (this.maxDeltaRevision_ = this.getRevision())),
                    ei(
                      this.flatCoordinates,
                      0,
                      this.ends_,
                      this.stride,
                      this.maxDelta_,
                      !1,
                      t,
                      e,
                      n,
                      i
                    ));
              }),
              (e.prototype.getCoordinateAtM = function (t, e, n) {
                if (
                  (this.layout != On && this.layout != Rn) ||
                  0 === this.flatCoordinates.length
                )
                  return null;
                var i = void 0 !== e && e,
                  r = void 0 !== n && n;
                return (function (t, e, n, i, r, o, s) {
                  if (s) return fi(t, e, n[n.length - 1], i, r, o);
                  var a;
                  if (r < t[i - 1])
                    return o ? (((a = t.slice(0, i))[i - 1] = r), a) : null;
                  if (t[t.length - 1] < r)
                    return o
                      ? (((a = t.slice(t.length - i))[i - 1] = r), a)
                      : null;
                  for (var l = 0, h = n.length; l < h; ++l) {
                    var u = n[l];
                    if (e != u) {
                      if (r < t[e + i - 1]) return null;
                      if (r <= t[u - 1]) return fi(t, e, u, i, r, !1);
                      e = u;
                    }
                  }
                  return null;
                })(this.flatCoordinates, 0, this.ends_, this.stride, t, i, r);
              }),
              (e.prototype.getCoordinates = function () {
                return ui(this.flatCoordinates, 0, this.ends_, this.stride);
              }),
              (e.prototype.getEnds = function () {
                return this.ends_;
              }),
              (e.prototype.getLineString = function (t) {
                return t < 0 || this.ends_.length <= t
                  ? null
                  : new wi(
                      this.flatCoordinates.slice(
                        0 === t ? 0 : this.ends_[t - 1],
                        this.ends_[t]
                      ),
                      this.layout
                    );
              }),
              (e.prototype.getLineStrings = function () {
                for (
                  var t = this.flatCoordinates,
                    e = this.ends_,
                    n = this.layout,
                    i = [],
                    r = 0,
                    o = 0,
                    s = e.length;
                  o < s;
                  ++o
                ) {
                  var a = e[o],
                    l = new wi(t.slice(r, a), n);
                  i.push(l), (r = a);
                }
                return i;
              }),
              (e.prototype.getFlatMidpoints = function () {
                for (
                  var t = [],
                    e = this.flatCoordinates,
                    n = 0,
                    i = this.ends_,
                    r = this.stride,
                    o = 0,
                    s = i.length;
                  o < s;
                  ++o
                ) {
                  var a = i[o];
                  l(t, pi(e, n, a, r, 0.5)), (n = a);
                }
                return t;
              }),
              (e.prototype.getSimplifiedGeometryInternal = function (t) {
                var n = [],
                  i = [];
                return (
                  (n.length = (function (t, e, n, i, r, o, s, a) {
                    for (var l = 0, h = n.length; l < h; ++l) {
                      var u = n[l];
                      (s = ri(t, e, u, i, r, o, s)), a.push(s), (e = u);
                    }
                    return s;
                  })(
                    this.flatCoordinates,
                    0,
                    this.ends_,
                    this.stride,
                    t,
                    n,
                    0,
                    i
                  )),
                  new e(n, bn, i)
                );
              }),
              (e.prototype.getType = function () {
                return Ln;
              }),
              (e.prototype.intersectsExtent = function (t) {
                return (function (t, e, n, i, r) {
                  for (var o = 0, s = n.length; o < s; ++o) {
                    if (yi(t, e, n[o], i, r)) return !0;
                    e = n[o];
                  }
                  return !1;
                })(this.flatCoordinates, 0, this.ends_, this.stride, t);
              }),
              (e.prototype.setCoordinates = function (t, e) {
                this.setLayout(e, t, 2),
                  this.flatCoordinates || (this.flatCoordinates = []);
                var n = ii(this.flatCoordinates, 0, t, this.stride, this.ends_);
                (this.flatCoordinates.length =
                  0 === n.length ? 0 : n[n.length - 1]),
                  this.changed();
              }),
              e
            );
          })(qn),
          Pi = Ii,
          Mi = (function () {
            var t = function (e, n) {
              return (
                (t =
                  Object.setPrototypeOf ||
                  ({ __proto__: [] } instanceof Array &&
                    function (t, e) {
                      t.__proto__ = e;
                    }) ||
                  function (t, e) {
                    for (var n in e)
                      Object.prototype.hasOwnProperty.call(e, n) &&
                        (t[n] = e[n]);
                  }),
                t(e, n)
              );
            };
            return function (e, n) {
              if ("function" != typeof n && null !== n)
                throw new TypeError(
                  "Class extends value " +
                    String(n) +
                    " is not a constructor or null"
                );
              function i() {
                this.constructor = e;
              }
              t(e, n),
                (e.prototype =
                  null === n
                    ? Object.create(n)
                    : ((i.prototype = n.prototype), new i()));
            };
          })(),
          Fi = (function (t) {
            function e(e, n) {
              var i = t.call(this) || this;
              return i.setCoordinates(e, n), i;
            }
            return (
              Mi(e, t),
              (e.prototype.clone = function () {
                var t = new e(this.flatCoordinates.slice(), this.layout);
                return t.applyProperties(this), t;
              }),
              (e.prototype.closestPointXY = function (t, e, n, i) {
                var r = this.flatCoordinates,
                  o = St(t, e, r[0], r[1]);
                if (o < i) {
                  for (var s = this.stride, a = 0; a < s; ++a) n[a] = r[a];
                  return (n.length = s), o;
                }
                return i;
              }),
              (e.prototype.getCoordinates = function () {
                return this.flatCoordinates ? this.flatCoordinates.slice() : [];
              }),
              (e.prototype.computeExtent = function (t) {
                return (
                  (n = t),
                  ve((i = (e = this.flatCoordinates)[0]), (r = e[1]), i, r, n)
                );
                var e, n, i, r;
              }),
              (e.prototype.getType = function () {
                return In;
              }),
              (e.prototype.intersectsExtent = function (t) {
                return _e(t, this.flatCoordinates[0], this.flatCoordinates[1]);
              }),
              (e.prototype.setCoordinates = function (t, e) {
                this.setLayout(e, t, 0),
                  this.flatCoordinates || (this.flatCoordinates = []),
                  (this.flatCoordinates.length = (function (t, e, n, i) {
                    for (var r = 0, o = n.length; r < o; ++r) t[e++] = n[r];
                    return e;
                  })(this.flatCoordinates, 0, t, this.stride)),
                  this.changed();
              }),
              e
            );
          })(qn),
          Li = Fi,
          Ai = (function () {
            var t = function (e, n) {
              return (
                (t =
                  Object.setPrototypeOf ||
                  ({ __proto__: [] } instanceof Array &&
                    function (t, e) {
                      t.__proto__ = e;
                    }) ||
                  function (t, e) {
                    for (var n in e)
                      Object.prototype.hasOwnProperty.call(e, n) &&
                        (t[n] = e[n]);
                  }),
                t(e, n)
              );
            };
            return function (e, n) {
              if ("function" != typeof n && null !== n)
                throw new TypeError(
                  "Class extends value " +
                    String(n) +
                    " is not a constructor or null"
                );
              function i() {
                this.constructor = e;
              }
              t(e, n),
                (e.prototype =
                  null === n
                    ? Object.create(n)
                    : ((i.prototype = n.prototype), new i()));
            };
          })(),
          Di = (function (t) {
            function e(e, n) {
              var i = t.call(this) || this;
              return (
                n && !Array.isArray(e[0])
                  ? i.setFlatCoordinates(n, e)
                  : i.setCoordinates(e, n),
                i
              );
            }
            return (
              Ai(e, t),
              (e.prototype.appendPoint = function (t) {
                this.flatCoordinates
                  ? l(this.flatCoordinates, t.getFlatCoordinates())
                  : (this.flatCoordinates = t.getFlatCoordinates().slice()),
                  this.changed();
              }),
              (e.prototype.clone = function () {
                var t = new e(this.flatCoordinates.slice(), this.layout);
                return t.applyProperties(this), t;
              }),
              (e.prototype.closestPointXY = function (t, e, n, i) {
                if (i < fe(this.getExtent(), t, e)) return i;
                for (
                  var r = this.flatCoordinates,
                    o = this.stride,
                    s = 0,
                    a = r.length;
                  s < a;
                  s += o
                ) {
                  var l = St(t, e, r[s], r[s + 1]);
                  if (l < i) {
                    i = l;
                    for (var h = 0; h < o; ++h) n[h] = r[s + h];
                    n.length = o;
                  }
                }
                return i;
              }),
              (e.prototype.getCoordinates = function () {
                return hi(
                  this.flatCoordinates,
                  0,
                  this.flatCoordinates.length,
                  this.stride
                );
              }),
              (e.prototype.getPoint = function (t) {
                var e = this.flatCoordinates
                  ? this.flatCoordinates.length / this.stride
                  : 0;
                return t < 0 || e <= t
                  ? null
                  : new Li(
                      this.flatCoordinates.slice(
                        t * this.stride,
                        (t + 1) * this.stride
                      ),
                      this.layout
                    );
              }),
              (e.prototype.getPoints = function () {
                for (
                  var t = this.flatCoordinates,
                    e = this.layout,
                    n = this.stride,
                    i = [],
                    r = 0,
                    o = t.length;
                  r < o;
                  r += n
                ) {
                  var s = new Li(t.slice(r, r + n), e);
                  i.push(s);
                }
                return i;
              }),
              (e.prototype.getType = function () {
                return Fn;
              }),
              (e.prototype.intersectsExtent = function (t) {
                for (
                  var e = this.flatCoordinates,
                    n = this.stride,
                    i = 0,
                    r = e.length;
                  i < r;
                  i += n
                )
                  if (_e(t, e[i], e[i + 1])) return !0;
                return !1;
              }),
              (e.prototype.setCoordinates = function (t, e) {
                this.setLayout(e, t, 1),
                  this.flatCoordinates || (this.flatCoordinates = []),
                  (this.flatCoordinates.length = ni(
                    this.flatCoordinates,
                    0,
                    t,
                    this.stride
                  )),
                  this.changed();
              }),
              e
            );
          })(qn),
          ki = Di;
        function ji(t, e, n, i, r, s, a) {
          for (
            var l, h, u, c, p, f, d, g = r[s + 1], _ = [], y = 0, v = n.length;
            y < v;
            ++y
          ) {
            var m = n[y];
            for (c = t[m - i], f = t[m - i + 1], l = e; l < m; l += i)
              (p = t[l]),
                (d = t[l + 1]),
                ((g <= f && d <= g) || (f <= g && g <= d)) &&
                  ((u = ((g - f) / (d - f)) * (p - c) + c), _.push(u)),
                (c = p),
                (f = d);
          }
          var x = NaN,
            C = -1 / 0;
          for (_.sort(o), c = _[0], l = 1, h = _.length; l < h; ++l) {
            p = _[l];
            var w = Math.abs(p - c);
            w > C && _i(t, e, n, i, (u = (c + p) / 2), g) && ((x = u), (C = w)),
              (c = p);
          }
          return isNaN(x) && (x = r[s]), a ? (a.push(x, g, C), a) : [x, g, C];
        }
        function Gi(t, e, n, i) {
          for (; e < n - i; ) {
            for (var r = 0; r < i; ++r) {
              var o = t[e + r];
              (t[e + r] = t[n - i + r]), (t[n - i + r] = o);
            }
            (e += i), (n -= i);
          }
        }
        function zi(t, e, n, i) {
          for (var r = 0, o = t[n - i], s = t[n - i + 1]; e < n; e += i) {
            var a = t[e],
              l = t[e + 1];
            (r += (a - o) * (l + s)), (o = a), (s = l);
          }
          return 0 === r ? void 0 : r > 0;
        }
        function Wi(t, e, n, i, r) {
          for (var o = void 0 !== r && r, s = 0, a = n.length; s < a; ++s) {
            var l = n[s],
              h = zi(t, e, l, i);
            if (0 === s) {
              if ((o && h) || (!o && !h)) return !1;
            } else if ((o && !h) || (!o && h)) return !1;
            e = l;
          }
          return !0;
        }
        function Xi(t, e, n, i, r) {
          for (var o = void 0 !== r && r, s = 0, a = n.length; s < a; ++s) {
            var l = n[s],
              h = zi(t, e, l, i);
            (0 === s ? (o && h) || (!o && !h) : (o && !h) || (!o && h)) &&
              Gi(t, e, l, i),
              (e = l);
          }
          return e;
        }
        function Ni(t, e, n, i, r) {
          for (var o = 0, s = n.length; o < s; ++o) e = Xi(t, e, n[o], i, r);
          return e;
        }
        var Yi = (function () {
            var t = function (e, n) {
              return (
                (t =
                  Object.setPrototypeOf ||
                  ({ __proto__: [] } instanceof Array &&
                    function (t, e) {
                      t.__proto__ = e;
                    }) ||
                  function (t, e) {
                    for (var n in e)
                      Object.prototype.hasOwnProperty.call(e, n) &&
                        (t[n] = e[n]);
                  }),
                t(e, n)
              );
            };
            return function (e, n) {
              if ("function" != typeof n && null !== n)
                throw new TypeError(
                  "Class extends value " +
                    String(n) +
                    " is not a constructor or null"
                );
              function i() {
                this.constructor = e;
              }
              t(e, n),
                (e.prototype =
                  null === n
                    ? Object.create(n)
                    : ((i.prototype = n.prototype), new i()));
            };
          })(),
          Bi = (function (t) {
            function e(e, n, i) {
              var r = t.call(this) || this;
              return (
                (r.ends_ = []),
                (r.flatInteriorPointRevision_ = -1),
                (r.flatInteriorPoint_ = null),
                (r.maxDelta_ = -1),
                (r.maxDeltaRevision_ = -1),
                (r.orientedRevision_ = -1),
                (r.orientedFlatCoordinates_ = null),
                void 0 !== n && i
                  ? (r.setFlatCoordinates(n, e), (r.ends_ = i))
                  : r.setCoordinates(e, n),
                r
              );
            }
            return (
              Yi(e, t),
              (e.prototype.appendLinearRing = function (t) {
                this.flatCoordinates
                  ? l(this.flatCoordinates, t.getFlatCoordinates())
                  : (this.flatCoordinates = t.getFlatCoordinates().slice()),
                  this.ends_.push(this.flatCoordinates.length),
                  this.changed();
              }),
              (e.prototype.clone = function () {
                var t = new e(
                  this.flatCoordinates.slice(),
                  this.layout,
                  this.ends_.slice()
                );
                return t.applyProperties(this), t;
              }),
              (e.prototype.closestPointXY = function (t, e, n, i) {
                return i < fe(this.getExtent(), t, e)
                  ? i
                  : (this.maxDeltaRevision_ != this.getRevision() &&
                      ((this.maxDelta_ = Math.sqrt(
                        $n(this.flatCoordinates, 0, this.ends_, this.stride, 0)
                      )),
                      (this.maxDeltaRevision_ = this.getRevision())),
                    ei(
                      this.flatCoordinates,
                      0,
                      this.ends_,
                      this.stride,
                      this.maxDelta_,
                      !0,
                      t,
                      e,
                      n,
                      i
                    ));
              }),
              (e.prototype.containsXY = function (t, e) {
                return _i(
                  this.getOrientedFlatCoordinates(),
                  0,
                  this.ends_,
                  this.stride,
                  t,
                  e
                );
              }),
              (e.prototype.getArea = function () {
                return Ei(
                  this.getOrientedFlatCoordinates(),
                  0,
                  this.ends_,
                  this.stride
                );
              }),
              (e.prototype.getCoordinates = function (t) {
                var e;
                return (
                  void 0 !== t
                    ? Xi(
                        (e = this.getOrientedFlatCoordinates().slice()),
                        0,
                        this.ends_,
                        this.stride,
                        t
                      )
                    : (e = this.flatCoordinates),
                  ui(e, 0, this.ends_, this.stride)
                );
              }),
              (e.prototype.getEnds = function () {
                return this.ends_;
              }),
              (e.prototype.getFlatInteriorPoint = function () {
                if (this.flatInteriorPointRevision_ != this.getRevision()) {
                  var t = Ie(this.getExtent());
                  (this.flatInteriorPoint_ = ji(
                    this.getOrientedFlatCoordinates(),
                    0,
                    this.ends_,
                    this.stride,
                    t,
                    0
                  )),
                    (this.flatInteriorPointRevision_ = this.getRevision());
                }
                return this.flatInteriorPoint_;
              }),
              (e.prototype.getInteriorPoint = function () {
                return new Li(this.getFlatInteriorPoint(), On);
              }),
              (e.prototype.getLinearRingCount = function () {
                return this.ends_.length;
              }),
              (e.prototype.getLinearRing = function (t) {
                return t < 0 || this.ends_.length <= t
                  ? null
                  : new Oi(
                      this.flatCoordinates.slice(
                        0 === t ? 0 : this.ends_[t - 1],
                        this.ends_[t]
                      ),
                      this.layout
                    );
              }),
              (e.prototype.getLinearRings = function () {
                for (
                  var t = this.layout,
                    e = this.flatCoordinates,
                    n = this.ends_,
                    i = [],
                    r = 0,
                    o = 0,
                    s = n.length;
                  o < s;
                  ++o
                ) {
                  var a = n[o],
                    l = new Oi(e.slice(r, a), t);
                  i.push(l), (r = a);
                }
                return i;
              }),
              (e.prototype.getOrientedFlatCoordinates = function () {
                if (this.orientedRevision_ != this.getRevision()) {
                  var t = this.flatCoordinates;
                  Wi(t, 0, this.ends_, this.stride)
                    ? (this.orientedFlatCoordinates_ = t)
                    : ((this.orientedFlatCoordinates_ = t.slice()),
                      (this.orientedFlatCoordinates_.length = Xi(
                        this.orientedFlatCoordinates_,
                        0,
                        this.ends_,
                        this.stride
                      ))),
                    (this.orientedRevision_ = this.getRevision());
                }
                return this.orientedFlatCoordinates_;
              }),
              (e.prototype.getSimplifiedGeometryInternal = function (t) {
                var n = [],
                  i = [];
                return (
                  (n.length = ai(
                    this.flatCoordinates,
                    0,
                    this.ends_,
                    this.stride,
                    Math.sqrt(t),
                    n,
                    0,
                    i
                  )),
                  new e(n, bn, i)
                );
              }),
              (e.prototype.getType = function () {
                return Mn;
              }),
              (e.prototype.intersectsExtent = function (t) {
                return vi(
                  this.getOrientedFlatCoordinates(),
                  0,
                  this.ends_,
                  this.stride,
                  t
                );
              }),
              (e.prototype.setCoordinates = function (t, e) {
                this.setLayout(e, t, 2),
                  this.flatCoordinates || (this.flatCoordinates = []);
                var n = ii(this.flatCoordinates, 0, t, this.stride, this.ends_);
                (this.flatCoordinates.length =
                  0 === n.length ? 0 : n[n.length - 1]),
                  this.changed();
              }),
              e
            );
          })(qn),
          Ki = Bi;
        function Zi(t) {
          var e = t[0],
            n = t[1],
            i = t[2],
            r = t[3],
            o = [e, n, e, r, i, r, i, n, e, n];
          return new Bi(o, bn, [o.length]);
        }
        var Vi = (function () {
            var t = function (e, n) {
              return (
                (t =
                  Object.setPrototypeOf ||
                  ({ __proto__: [] } instanceof Array &&
                    function (t, e) {
                      t.__proto__ = e;
                    }) ||
                  function (t, e) {
                    for (var n in e)
                      Object.prototype.hasOwnProperty.call(e, n) &&
                        (t[n] = e[n]);
                  }),
                t(e, n)
              );
            };
            return function (e, n) {
              if ("function" != typeof n && null !== n)
                throw new TypeError(
                  "Class extends value " +
                    String(n) +
                    " is not a constructor or null"
                );
              function i() {
                this.constructor = e;
              }
              t(e, n),
                (e.prototype =
                  null === n
                    ? Object.create(n)
                    : ((i.prototype = n.prototype), new i()));
            };
          })(),
          Ui = (function (t) {
            function e(e, n, i) {
              var r = t.call(this) || this;
              if (
                ((r.endss_ = []),
                (r.flatInteriorPointsRevision_ = -1),
                (r.flatInteriorPoints_ = null),
                (r.maxDelta_ = -1),
                (r.maxDeltaRevision_ = -1),
                (r.orientedRevision_ = -1),
                (r.orientedFlatCoordinates_ = null),
                !i && !Array.isArray(e[0]))
              ) {
                for (
                  var o = r.getLayout(),
                    s = e,
                    a = [],
                    h = [],
                    u = 0,
                    c = s.length;
                  u < c;
                  ++u
                ) {
                  var p = s[u];
                  0 === u && (o = p.getLayout());
                  for (
                    var f = a.length, d = p.getEnds(), g = 0, _ = d.length;
                    g < _;
                    ++g
                  )
                    d[g] += f;
                  l(a, p.getFlatCoordinates()), h.push(d);
                }
                (n = o), (e = a), (i = h);
              }
              return (
                void 0 !== n && i
                  ? (r.setFlatCoordinates(n, e), (r.endss_ = i))
                  : r.setCoordinates(e, n),
                r
              );
            }
            return (
              Vi(e, t),
              (e.prototype.appendPolygon = function (t) {
                var e;
                if (this.flatCoordinates) {
                  var n = this.flatCoordinates.length;
                  l(this.flatCoordinates, t.getFlatCoordinates());
                  for (
                    var i = 0, r = (e = t.getEnds().slice()).length;
                    i < r;
                    ++i
                  )
                    e[i] += n;
                } else
                  (this.flatCoordinates = t.getFlatCoordinates().slice()),
                    (e = t.getEnds().slice()),
                    this.endss_.push();
                this.endss_.push(e), this.changed();
              }),
              (e.prototype.clone = function () {
                for (
                  var t = this.endss_.length, n = new Array(t), i = 0;
                  i < t;
                  ++i
                )
                  n[i] = this.endss_[i].slice();
                var r = new e(this.flatCoordinates.slice(), this.layout, n);
                return r.applyProperties(this), r;
              }),
              (e.prototype.closestPointXY = function (t, e, n, i) {
                return i < fe(this.getExtent(), t, e)
                  ? i
                  : (this.maxDeltaRevision_ != this.getRevision() &&
                      ((this.maxDelta_ = Math.sqrt(
                        (function (t, e, n, i, r) {
                          for (var o = 0, s = n.length; o < s; ++o) {
                            var a = n[o];
                            (r = $n(t, e, a, i, r)), (e = a[a.length - 1]);
                          }
                          return r;
                        })(this.flatCoordinates, 0, this.endss_, this.stride, 0)
                      )),
                      (this.maxDeltaRevision_ = this.getRevision())),
                    (function (t, e, n, i, r, o, s, a, l, h, u) {
                      for (
                        var c = [NaN, NaN], p = 0, f = n.length;
                        p < f;
                        ++p
                      ) {
                        var d = n[p];
                        (h = ei(t, e, d, i, r, true, s, a, l, h, c)),
                          (e = d[d.length - 1]);
                      }
                      return h;
                    })(
                      this.getOrientedFlatCoordinates(),
                      0,
                      this.endss_,
                      this.stride,
                      this.maxDelta_,
                      0,
                      t,
                      e,
                      n,
                      i
                    ));
              }),
              (e.prototype.containsXY = function (t, e) {
                return (function (t, e, n, i, r, o) {
                  if (0 === n.length) return !1;
                  for (var s = 0, a = n.length; s < a; ++s) {
                    var l = n[s];
                    if (_i(t, e, l, i, r, o)) return !0;
                    e = l[l.length - 1];
                  }
                  return !1;
                })(
                  this.getOrientedFlatCoordinates(),
                  0,
                  this.endss_,
                  this.stride,
                  t,
                  e
                );
              }),
              (e.prototype.getArea = function () {
                return (function (t, e, n, i) {
                  for (var r = 0, o = 0, s = n.length; o < s; ++o) {
                    var a = n[o];
                    (r += Ei(t, e, a, i)), (e = a[a.length - 1]);
                  }
                  return r;
                })(
                  this.getOrientedFlatCoordinates(),
                  0,
                  this.endss_,
                  this.stride
                );
              }),
              (e.prototype.getCoordinates = function (t) {
                var e;
                return (
                  void 0 !== t
                    ? Ni(
                        (e = this.getOrientedFlatCoordinates().slice()),
                        0,
                        this.endss_,
                        this.stride,
                        t
                      )
                    : (e = this.flatCoordinates),
                  ci(e, 0, this.endss_, this.stride)
                );
              }),
              (e.prototype.getEndss = function () {
                return this.endss_;
              }),
              (e.prototype.getFlatInteriorPoints = function () {
                if (this.flatInteriorPointsRevision_ != this.getRevision()) {
                  var t = (function (t, e, n, i) {
                    for (
                      var r = [],
                        o = [1 / 0, 1 / 0, -1 / 0, -1 / 0],
                        s = 0,
                        a = n.length;
                      s < a;
                      ++s
                    ) {
                      var l = n[s];
                      (o = xe(t, e, l[0], i)),
                        r.push((o[0] + o[2]) / 2, (o[1] + o[3]) / 2),
                        (e = l[l.length - 1]);
                    }
                    return r;
                  })(this.flatCoordinates, 0, this.endss_, this.stride);
                  (this.flatInteriorPoints_ = (function (t, e, n, i, r) {
                    for (var o = [], s = 0, a = n.length; s < a; ++s) {
                      var l = n[s];
                      (o = ji(t, e, l, i, r, 2 * s, o)), (e = l[l.length - 1]);
                    }
                    return o;
                  })(
                    this.getOrientedFlatCoordinates(),
                    0,
                    this.endss_,
                    this.stride,
                    t
                  )),
                    (this.flatInteriorPointsRevision_ = this.getRevision());
                }
                return this.flatInteriorPoints_;
              }),
              (e.prototype.getInteriorPoints = function () {
                return new ki(this.getFlatInteriorPoints().slice(), On);
              }),
              (e.prototype.getOrientedFlatCoordinates = function () {
                if (this.orientedRevision_ != this.getRevision()) {
                  var t = this.flatCoordinates;
                  !(function (t, e, n, i, r) {
                    for (var o = 0, s = n.length; o < s; ++o) {
                      var a = n[o];
                      if (!Wi(t, e, a, i, undefined)) return !1;
                      a.length && (e = a[a.length - 1]);
                    }
                    return !0;
                  })(t, 0, this.endss_, this.stride)
                    ? ((this.orientedFlatCoordinates_ = t.slice()),
                      (this.orientedFlatCoordinates_.length = Ni(
                        this.orientedFlatCoordinates_,
                        0,
                        this.endss_,
                        this.stride
                      )))
                    : (this.orientedFlatCoordinates_ = t),
                    (this.orientedRevision_ = this.getRevision());
                }
                return this.orientedFlatCoordinates_;
              }),
              (e.prototype.getSimplifiedGeometryInternal = function (t) {
                var n = [],
                  i = [];
                return (
                  (n.length = (function (t, e, n, i, r, o, s, a) {
                    for (var l = 0, h = n.length; l < h; ++l) {
                      var u = n[l],
                        c = [];
                      (s = ai(t, e, u, i, r, o, s, c)),
                        a.push(c),
                        (e = u[u.length - 1]);
                    }
                    return s;
                  })(
                    this.flatCoordinates,
                    0,
                    this.endss_,
                    this.stride,
                    Math.sqrt(t),
                    n,
                    0,
                    i
                  )),
                  new e(n, bn, i)
                );
              }),
              (e.prototype.getPolygon = function (t) {
                if (t < 0 || this.endss_.length <= t) return null;
                var e;
                if (0 === t) e = 0;
                else {
                  var n = this.endss_[t - 1];
                  e = n[n.length - 1];
                }
                var i = this.endss_[t].slice(),
                  r = i[i.length - 1];
                if (0 !== e)
                  for (var o = 0, s = i.length; o < s; ++o) i[o] -= e;
                return new Ki(this.flatCoordinates.slice(e, r), this.layout, i);
              }),
              (e.prototype.getPolygons = function () {
                for (
                  var t = this.layout,
                    e = this.flatCoordinates,
                    n = this.endss_,
                    i = [],
                    r = 0,
                    o = 0,
                    s = n.length;
                  o < s;
                  ++o
                ) {
                  var a = n[o].slice(),
                    l = a[a.length - 1];
                  if (0 !== r)
                    for (var h = 0, u = a.length; h < u; ++h) a[h] -= r;
                  var c = new Ki(e.slice(r, l), t, a);
                  i.push(c), (r = l);
                }
                return i;
              }),
              (e.prototype.getType = function () {
                return An;
              }),
              (e.prototype.intersectsExtent = function (t) {
                return (function (t, e, n, i, r) {
                  for (var o = 0, s = n.length; o < s; ++o) {
                    var a = n[o];
                    if (vi(t, e, a, i, r)) return !0;
                    e = a[a.length - 1];
                  }
                  return !1;
                })(
                  this.getOrientedFlatCoordinates(),
                  0,
                  this.endss_,
                  this.stride,
                  t
                );
              }),
              (e.prototype.setCoordinates = function (t, e) {
                this.setLayout(e, t, 3),
                  this.flatCoordinates || (this.flatCoordinates = []);
                var n = (function (t, e, n, i, r) {
                  for (
                    var o = r || [], s = 0, a = 0, l = n.length;
                    a < l;
                    ++a
                  ) {
                    var h = ii(t, e, n[a], i, o[s]);
                    (o[s++] = h), (e = h[h.length - 1]);
                  }
                  return (o.length = s), o;
                })(this.flatCoordinates, 0, t, this.stride, this.endss_);
                if (0 === n.length) this.flatCoordinates.length = 0;
                else {
                  var i = n[n.length - 1];
                  this.flatCoordinates.length =
                    0 === i.length ? 0 : i[i.length - 1];
                }
                this.changed();
              }),
              e
            );
          })(qn),
          Hi = Ui,
          qi = "preload",
          Ji = "useInterimTilesOnError",
          Qi = (function () {
            var t = function (e, n) {
              return (
                (t =
                  Object.setPrototypeOf ||
                  ({ __proto__: [] } instanceof Array &&
                    function (t, e) {
                      t.__proto__ = e;
                    }) ||
                  function (t, e) {
                    for (var n in e)
                      Object.prototype.hasOwnProperty.call(e, n) &&
                        (t[n] = e[n]);
                  }),
                t(e, n)
              );
            };
            return function (e, n) {
              if ("function" != typeof n && null !== n)
                throw new TypeError(
                  "Class extends value " +
                    String(n) +
                    " is not a constructor or null"
                );
              function i() {
                this.constructor = e;
              }
              t(e, n),
                (e.prototype =
                  null === n
                    ? Object.create(n)
                    : ((i.prototype = n.prototype), new i()));
            };
          })(),
          $i = (function (t) {
            function e(e) {
              var n = this,
                i = e || {},
                r = f({}, i);
              return (
                delete r.preload,
                delete r.useInterimTilesOnError,
                (n = t.call(this, r) || this).on,
                n.once,
                n.un,
                n.setPreload(void 0 !== i.preload ? i.preload : 0),
                n.setUseInterimTilesOnError(
                  void 0 === i.useInterimTilesOnError ||
                    i.useInterimTilesOnError
                ),
                n
              );
            }
            return (
              Qi(e, t),
              (e.prototype.getPreload = function () {
                return this.get(qi);
              }),
              (e.prototype.setPreload = function (t) {
                this.set(qi, t);
              }),
              (e.prototype.getUseInterimTilesOnError = function () {
                return this.get(Ji);
              }),
              (e.prototype.setUseInterimTilesOnError = function (t) {
                this.set(Ji, t);
              }),
              (e.prototype.getData = function (e) {
                return t.prototype.getData.call(this, e);
              }),
              e
            );
          })(Gt),
          tr = (function () {
            var t = function (e, n) {
              return (
                (t =
                  Object.setPrototypeOf ||
                  ({ __proto__: [] } instanceof Array &&
                    function (t, e) {
                      t.__proto__ = e;
                    }) ||
                  function (t, e) {
                    for (var n in e)
                      Object.prototype.hasOwnProperty.call(e, n) &&
                        (t[n] = e[n]);
                  }),
                t(e, n)
              );
            };
            return function (e, n) {
              if ("function" != typeof n && null !== n)
                throw new TypeError(
                  "Class extends value " +
                    String(n) +
                    " is not a constructor or null"
                );
              function i() {
                this.constructor = e;
              }
              t(e, n),
                (e.prototype =
                  null === n
                    ? Object.create(n)
                    : ((i.prototype = n.prototype), new i()));
            };
          })(),
          er = (function (t) {
            function e(e) {
              var n = t.call(this) || this;
              return (
                (n.ready = !0),
                (n.boundHandleImageChange_ = n.handleImageChange_.bind(n)),
                (n.layer_ = e),
                (n.declutterExecutorGroup = null),
                n
              );
            }
            return (
              tr(e, t),
              (e.prototype.getFeatures = function (t) {
                return L();
              }),
              (e.prototype.getData = function (t) {
                return null;
              }),
              (e.prototype.prepareFrame = function (t) {
                return L();
              }),
              (e.prototype.renderFrame = function (t, e) {
                return L();
              }),
              (e.prototype.loadedTileCallback = function (t, e, n) {
                t[e] || (t[e] = {}), (t[e][n.tileCoord.toString()] = n);
              }),
              (e.prototype.createLoadedTileFinder = function (t, e, n) {
                return function (i, r) {
                  var o = this.loadedTileCallback.bind(this, n, i);
                  return t.forEachLoadedTile(e, i, r, o);
                }.bind(this);
              }),
              (e.prototype.forEachFeatureAtCoordinate = function (
                t,
                e,
                n,
                i,
                r
              ) {}),
              (e.prototype.getDataAtPixel = function (t, e, n) {
                return null;
              }),
              (e.prototype.getLayer = function () {
                return this.layer_;
              }),
              (e.prototype.handleFontsChanged = function () {}),
              (e.prototype.handleImageChange_ = function (t) {
                2 === t.target.getState() && this.renderIfReadyAndVisible();
              }),
              (e.prototype.loadImage = function (t) {
                var e = t.getState();
                return (
                  2 != e &&
                    3 != e &&
                    t.addEventListener(x, this.boundHandleImageChange_),
                  0 == e && (t.load(), (e = t.getState())),
                  2 == e
                );
              }),
              (e.prototype.renderIfReadyAndVisible = function () {
                var t = this.getLayer();
                t.getVisible() && t.getSourceState() == Dt && t.changed();
              }),
              (e.prototype.disposeInternal = function () {
                delete this.layer_, t.prototype.disposeInternal.call(this);
              }),
              e
            );
          })(F),
          nr = (function () {
            var t = function (e, n) {
              return (
                (t =
                  Object.setPrototypeOf ||
                  ({ __proto__: [] } instanceof Array &&
                    function (t, e) {
                      t.__proto__ = e;
                    }) ||
                  function (t, e) {
                    for (var n in e)
                      Object.prototype.hasOwnProperty.call(e, n) &&
                        (t[n] = e[n]);
                  }),
                t(e, n)
              );
            };
            return function (e, n) {
              if ("function" != typeof n && null !== n)
                throw new TypeError(
                  "Class extends value " +
                    String(n) +
                    " is not a constructor or null"
                );
              function i() {
                this.constructor = e;
              }
              t(e, n),
                (e.prototype =
                  null === n
                    ? Object.create(n)
                    : ((i.prototype = n.prototype), new i()));
            };
          })(),
          ir = (function (t) {
            function e(e, n, i, r) {
              var o = t.call(this, e) || this;
              return (
                (o.inversePixelTransform = n),
                (o.frameState = i),
                (o.context = r),
                o
              );
            }
            return nr(e, t), e;
          })(t),
          rr = /^#([a-f0-9]{3}|[a-f0-9]{4}(?:[a-f0-9]{2}){0,2})$/i,
          or = /^([a-z]*)$|^hsla?\(.*\)$/i;
        function sr(t) {
          return "string" == typeof t ? t : ur(t);
        }
        var ar = (function () {
          var t = {},
            e = 0;
          return function (n) {
            var i;
            if (t.hasOwnProperty(n)) i = t[n];
            else {
              if (e >= 1024) {
                var r = 0;
                for (var o in t) 0 == (3 & r++) && (delete t[o], --e);
              }
              (i = (function (t) {
                var e, n, i, r, o;
                if (
                  (or.exec(t) &&
                    (t = (function (t) {
                      var e = document.createElement("div");
                      if (((e.style.color = t), "" !== e.style.color)) {
                        document.body.appendChild(e);
                        var n = getComputedStyle(e).color;
                        return document.body.removeChild(e), n;
                      }
                      return "";
                    })(t)),
                  rr.exec(t))
                ) {
                  var s,
                    a = t.length - 1;
                  s = a <= 4 ? 1 : 2;
                  var l = 4 === a || 8 === a;
                  (e = parseInt(t.substr(1 + 0 * s, s), 16)),
                    (n = parseInt(t.substr(1 + 1 * s, s), 16)),
                    (i = parseInt(t.substr(1 + 2 * s, s), 16)),
                    (r = l ? parseInt(t.substr(1 + 3 * s, s), 16) : 255),
                    1 == s &&
                      ((e = (e << 4) + e),
                      (n = (n << 4) + n),
                      (i = (i << 4) + i),
                      l && (r = (r << 4) + r)),
                    (o = [e, n, i, r / 255]);
                } else
                  0 == t.indexOf("rgba(")
                    ? hr((o = t.slice(5, -1).split(",").map(Number)))
                    : 0 == t.indexOf("rgb(")
                    ? ((o = t.slice(4, -1).split(",").map(Number)).push(1),
                      hr(o))
                    : vt(!1, 14);
                return o;
              })(n)),
                (t[n] = i),
                ++e;
            }
            return i;
          };
        })();
        function lr(t) {
          return Array.isArray(t) ? t : ar(t);
        }
        function hr(t) {
          return (
            (t[0] = mt((t[0] + 0.5) | 0, 0, 255)),
            (t[1] = mt((t[1] + 0.5) | 0, 0, 255)),
            (t[2] = mt((t[2] + 0.5) | 0, 0, 255)),
            (t[3] = mt(t[3], 0, 1)),
            t
          );
        }
        function ur(t) {
          var e = t[0];
          e != (0 | e) && (e = (e + 0.5) | 0);
          var n = t[1];
          n != (0 | n) && (n = (n + 0.5) | 0);
          var i = t[2];
          return (
            i != (0 | i) && (i = (i + 0.5) | 0),
            "rgba(" +
              e +
              "," +
              n +
              "," +
              i +
              "," +
              (void 0 === t[3] ? 1 : Math.round(100 * t[3]) / 100) +
              ")"
          );
        }
        var cr = (function () {
            var t = function (e, n) {
              return (
                (t =
                  Object.setPrototypeOf ||
                  ({ __proto__: [] } instanceof Array &&
                    function (t, e) {
                      t.__proto__ = e;
                    }) ||
                  function (t, e) {
                    for (var n in e)
                      Object.prototype.hasOwnProperty.call(e, n) &&
                        (t[n] = e[n]);
                  }),
                t(e, n)
              );
            };
            return function (e, n) {
              if ("function" != typeof n && null !== n)
                throw new TypeError(
                  "Class extends value " +
                    String(n) +
                    " is not a constructor or null"
                );
              function i() {
                this.constructor = e;
              }
              t(e, n),
                (e.prototype =
                  null === n
                    ? Object.create(n)
                    : ((i.prototype = n.prototype), new i()));
            };
          })(),
          pr = null,
          fr = (function (t) {
            function e(e) {
              var n = t.call(this, e) || this;
              return (
                (n.container = null),
                n.renderedResolution,
                (n.tempTransform = [1, 0, 0, 1, 0, 0]),
                (n.pixelTransform = [1, 0, 0, 1, 0, 0]),
                (n.inversePixelTransform = [1, 0, 0, 1, 0, 0]),
                (n.context = null),
                (n.containerReused = !1),
                (n.pixelContext_ = null),
                (n.frameState = null),
                n
              );
            }
            return (
              cr(e, t),
              (e.prototype.getImageData = function (t, e, n) {
                var i, r;
                pr ||
                  (((i = document.createElement("canvas")).width = 1),
                  (i.height = 1),
                  (pr = i.getContext("2d"))),
                  pr.clearRect(0, 0, 1, 1);
                try {
                  pr.drawImage(t, e, n, 1, 1, 0, 0, 1, 1),
                    (r = pr.getImageData(0, 0, 1, 1).data);
                } catch (t) {
                  return null;
                }
                return r;
              }),
              (e.prototype.getBackground = function (t) {
                var e = this.getLayer().getBackground();
                return (
                  "function" == typeof e && (e = e(t.viewState.resolution)),
                  e || void 0
                );
              }),
              (e.prototype.useContainer = function (t, e, n, i) {
                var r,
                  o,
                  s = this.getLayer().getClassName();
                if (
                  (t &&
                    t.className === s &&
                    "" === t.style.opacity &&
                    1 === n &&
                    (!i ||
                      (t.style.backgroundColor &&
                        h(lr(t.style.backgroundColor), lr(i)))) &&
                    (l = t.firstElementChild) instanceof HTMLCanvasElement &&
                    (o = l.getContext("2d")),
                  o && o.canvas.style.transform === e
                    ? ((this.container = t),
                      (this.context = o),
                      (this.containerReused = !0))
                    : this.containerReused &&
                      ((this.container = null),
                      (this.context = null),
                      (this.containerReused = !1)),
                  !this.container)
                ) {
                  (r = document.createElement("div")).className = s;
                  var a = r.style;
                  (a.position = "absolute"),
                    (a.width = "100%"),
                    (a.height = "100%"),
                    i && (a.backgroundColor = i);
                  var l = (o = q()).canvas;
                  r.appendChild(l),
                    ((a = l.style).position = "absolute"),
                    (a.left = "0"),
                    (a.transformOrigin = "top left"),
                    (this.container = r),
                    (this.context = o);
                }
              }),
              (e.prototype.clipUnrotated = function (t, e, n) {
                var i = Ae(n),
                  r = De(n),
                  o = Re(n),
                  s = Oe(n);
                jn(e.coordinateToPixelTransform, i),
                  jn(e.coordinateToPixelTransform, r),
                  jn(e.coordinateToPixelTransform, o),
                  jn(e.coordinateToPixelTransform, s);
                var a = this.inversePixelTransform;
                jn(a, i),
                  jn(a, r),
                  jn(a, o),
                  jn(a, s),
                  t.save(),
                  t.beginPath(),
                  t.moveTo(Math.round(i[0]), Math.round(i[1])),
                  t.lineTo(Math.round(r[0]), Math.round(r[1])),
                  t.lineTo(Math.round(o[0]), Math.round(o[1])),
                  t.lineTo(Math.round(s[0]), Math.round(s[1])),
                  t.clip();
              }),
              (e.prototype.dispatchRenderEvent_ = function (t, e, n) {
                var i = this.getLayer();
                if (i.hasListener(t)) {
                  var r = new ir(t, this.inversePixelTransform, n, e);
                  i.dispatchEvent(r);
                }
              }),
              (e.prototype.preRender = function (t, e) {
                (this.frameState = e),
                  this.dispatchRenderEvent_("prerender", t, e);
              }),
              (e.prototype.postRender = function (t, e) {
                this.dispatchRenderEvent_("postrender", t, e);
              }),
              (e.prototype.getRenderTransform = function (t, e, n, i, r, o, s) {
                var a = r / 2,
                  l = o / 2,
                  h = i / e,
                  u = -h,
                  c = -t[0] + s,
                  p = -t[1];
                return Gn(this.tempTransform, a, l, h, u, -n, c, p);
              }),
              (e.prototype.getDataAtPixel = function (t, e, n) {
                var i = jn(this.inversePixelTransform, t.slice()),
                  r = this.context,
                  o = this.getLayer().getExtent();
                if (o && !de(o, jn(e.pixelToCoordinateTransform, t.slice())))
                  return null;
                var s,
                  a = Math.round(i[0]),
                  l = Math.round(i[1]),
                  h = this.pixelContext_;
                if (!h) {
                  var u = document.createElement("canvas");
                  (u.width = 1),
                    (u.height = 1),
                    (h = u.getContext("2d")),
                    (this.pixelContext_ = h);
                }
                h.clearRect(0, 0, 1, 1);
                try {
                  h.drawImage(r.canvas, a, l, 1, 1, 0, 0, 1, 1),
                    (s = h.getImageData(0, 0, 1, 1).data);
                } catch (t) {
                  return "SecurityError" === t.name
                    ? ((this.pixelContext_ = null), new Uint8Array())
                    : s;
                }
                return 0 === s[3] ? null : s;
              }),
              (e.prototype.disposeInternal = function () {
                delete this.frameState, t.prototype.disposeInternal.call(this);
              }),
              e
            );
          })(er),
          dr = fr,
          gr = (function () {
            var t = function (e, n) {
              return (
                (t =
                  Object.setPrototypeOf ||
                  ({ __proto__: [] } instanceof Array &&
                    function (t, e) {
                      t.__proto__ = e;
                    }) ||
                  function (t, e) {
                    for (var n in e)
                      Object.prototype.hasOwnProperty.call(e, n) &&
                        (t[n] = e[n]);
                  }),
                t(e, n)
              );
            };
            return function (e, n) {
              if ("function" != typeof n && null !== n)
                throw new TypeError(
                  "Class extends value " +
                    String(n) +
                    " is not a constructor or null"
                );
              function i() {
                this.constructor = e;
              }
              t(e, n),
                (e.prototype =
                  null === n
                    ? Object.create(n)
                    : ((i.prototype = n.prototype), new i()));
            };
          })(),
          _r = (function (t) {
            function e(e, n, i) {
              var r = t.call(this) || this,
                o = i || {};
              return (
                (r.tileCoord = e),
                (r.state = n),
                (r.interimTile = null),
                (r.key = ""),
                (r.transition_ = void 0 === o.transition ? 250 : o.transition),
                (r.transitionStarts_ = {}),
                (r.interpolate = !!o.interpolate),
                r
              );
            }
            return (
              gr(e, t),
              (e.prototype.changed = function () {
                this.dispatchEvent(x);
              }),
              (e.prototype.release = function () {}),
              (e.prototype.getKey = function () {
                return this.key + "/" + this.tileCoord;
              }),
              (e.prototype.getInterimTile = function () {
                if (!this.interimTile) return this;
                var t = this.interimTile;
                do {
                  if (2 == t.getState()) return (this.transition_ = 0), t;
                  t = t.interimTile;
                } while (t);
                return this;
              }),
              (e.prototype.refreshInterimChain = function () {
                if (this.interimTile) {
                  var t = this.interimTile,
                    e = this;
                  do {
                    if (2 == t.getState()) {
                      t.interimTile = null;
                      break;
                    }
                    1 == t.getState()
                      ? (e = t)
                      : 0 == t.getState()
                      ? (e.interimTile = t.interimTile)
                      : (e = t),
                      (t = e.interimTile);
                  } while (t);
                }
              }),
              (e.prototype.getTileCoord = function () {
                return this.tileCoord;
              }),
              (e.prototype.getState = function () {
                return this.state;
              }),
              (e.prototype.setState = function (t) {
                if (3 !== this.state && this.state > t)
                  throw new Error("Tile load sequence violation");
                (this.state = t), this.changed();
              }),
              (e.prototype.load = function () {
                L();
              }),
              (e.prototype.getAlpha = function (t, e) {
                if (!this.transition_) return 1;
                var n = this.transitionStarts_[t];
                if (n) {
                  if (-1 === n) return 1;
                } else (n = e), (this.transitionStarts_[t] = n);
                var i = e - n + 1e3 / 60;
                return i >= this.transition_ ? 1 : mn(i / this.transition_);
              }),
              (e.prototype.inTransition = function (t) {
                return !!this.transition_ && -1 !== this.transitionStarts_[t];
              }),
              (e.prototype.endTransition = function (t) {
                this.transition_ && (this.transitionStarts_[t] = -1);
              }),
              e
            );
          })(m),
          yr = (function () {
            var t = function (e, n) {
              return (
                (t =
                  Object.setPrototypeOf ||
                  ({ __proto__: [] } instanceof Array &&
                    function (t, e) {
                      t.__proto__ = e;
                    }) ||
                  function (t, e) {
                    for (var n in e)
                      Object.prototype.hasOwnProperty.call(e, n) &&
                        (t[n] = e[n]);
                  }),
                t(e, n)
              );
            };
            return function (e, n) {
              if ("function" != typeof n && null !== n)
                throw new TypeError(
                  "Class extends value " +
                    String(n) +
                    " is not a constructor or null"
                );
              function i() {
                this.constructor = e;
              }
              t(e, n),
                (e.prototype =
                  null === n
                    ? Object.create(n)
                    : ((i.prototype = n.prototype), new i()));
            };
          })(),
          vr = (function (t) {
            function e(e, n, i, r) {
              var o = t.call(this) || this;
              return (
                (o.extent = e),
                (o.pixelRatio_ = i),
                (o.resolution = n),
                (o.state = r),
                o
              );
            }
            return (
              yr(e, t),
              (e.prototype.changed = function () {
                this.dispatchEvent(x);
              }),
              (e.prototype.getExtent = function () {
                return this.extent;
              }),
              (e.prototype.getImage = function () {
                return L();
              }),
              (e.prototype.getPixelRatio = function () {
                return this.pixelRatio_;
              }),
              (e.prototype.getResolution = function () {
                return this.resolution;
              }),
              (e.prototype.getState = function () {
                return this.state;
              }),
              (e.prototype.load = function () {
                L();
              }),
              e
            );
          })(m),
          mr = (function () {
            var t = function (e, n) {
              return (
                (t =
                  Object.setPrototypeOf ||
                  ({ __proto__: [] } instanceof Array &&
                    function (t, e) {
                      t.__proto__ = e;
                    }) ||
                  function (t, e) {
                    for (var n in e)
                      Object.prototype.hasOwnProperty.call(e, n) &&
                        (t[n] = e[n]);
                  }),
                t(e, n)
              );
            };
            return function (e, n) {
              if ("function" != typeof n && null !== n)
                throw new TypeError(
                  "Class extends value " +
                    String(n) +
                    " is not a constructor or null"
                );
              function i() {
                this.constructor = e;
              }
              t(e, n),
                (e.prototype =
                  null === n
                    ? Object.create(n)
                    : ((i.prototype = n.prototype), new i()));
            };
          })();
        function xr(t, e, n) {
          var i = t,
            r = !0,
            o = !1,
            s = !1,
            a = [
              R(i, "load", function () {
                (s = !0), o || e();
              }),
            ];
          return (
            i.src && U
              ? ((o = !0),
                i
                  .decode()
                  .then(function () {
                    r && e();
                  })
                  .catch(function (t) {
                    r && (s ? e() : n());
                  }))
              : a.push(R(i, "error", n)),
            function () {
              (r = !1), a.forEach(I);
            }
          );
        }
        !(function (t) {
          function e(e, n, i, r, o, s) {
            var a = t.call(this, e, n, i, 0) || this;
            return (
              (a.src_ = r),
              (a.image_ = new Image()),
              null !== o && (a.image_.crossOrigin = o),
              (a.unlisten_ = null),
              (a.state = 0),
              (a.imageLoadFunction_ = s),
              a
            );
          }
          mr(e, t),
            (e.prototype.getImage = function () {
              return this.image_;
            }),
            (e.prototype.handleImageError_ = function () {
              (this.state = 3), this.unlistenImage_(), this.changed();
            }),
            (e.prototype.handleImageLoad_ = function () {
              void 0 === this.resolution &&
                (this.resolution = Fe(this.extent) / this.image_.height),
                (this.state = 2),
                this.unlistenImage_(),
                this.changed();
            }),
            (e.prototype.load = function () {
              (0 != this.state && 3 != this.state) ||
                ((this.state = 1),
                this.changed(),
                this.imageLoadFunction_(this, this.src_),
                (this.unlisten_ = xr(
                  this.image_,
                  this.handleImageLoad_.bind(this),
                  this.handleImageError_.bind(this)
                )));
            }),
            (e.prototype.setImage = function (t) {
              (this.image_ = t),
                (this.resolution = Fe(this.extent) / this.image_.height);
            }),
            (e.prototype.unlistenImage_ = function () {
              this.unlisten_ && (this.unlisten_(), (this.unlisten_ = null));
            });
        })(vr);
        var Cr,
          wr = (function () {
            var t = function (e, n) {
              return (
                (t =
                  Object.setPrototypeOf ||
                  ({ __proto__: [] } instanceof Array &&
                    function (t, e) {
                      t.__proto__ = e;
                    }) ||
                  function (t, e) {
                    for (var n in e)
                      Object.prototype.hasOwnProperty.call(e, n) &&
                        (t[n] = e[n]);
                  }),
                t(e, n)
              );
            };
            return function (e, n) {
              if ("function" != typeof n && null !== n)
                throw new TypeError(
                  "Class extends value " +
                    String(n) +
                    " is not a constructor or null"
                );
              function i() {
                this.constructor = e;
              }
              t(e, n),
                (e.prototype =
                  null === n
                    ? Object.create(n)
                    : ((i.prototype = n.prototype), new i()));
            };
          })(),
          Sr = (function (t) {
            function e(e, n, i, r, o, s) {
              var a = t.call(this, e, n, s) || this;
              return (
                (a.crossOrigin_ = r),
                (a.src_ = i),
                (a.key = i),
                (a.image_ = new Image()),
                null !== r && (a.image_.crossOrigin = r),
                (a.unlisten_ = null),
                (a.tileLoadFunction_ = o),
                a
              );
            }
            return (
              wr(e, t),
              (e.prototype.getImage = function () {
                return this.image_;
              }),
              (e.prototype.setImage = function (t) {
                (this.image_ = t),
                  (this.state = 2),
                  this.unlistenImage_(),
                  this.changed();
              }),
              (e.prototype.handleImageError_ = function () {
                var t;
                (this.state = 3),
                  this.unlistenImage_(),
                  (this.image_ =
                    (((t = q(1, 1)).fillStyle = "rgba(0,0,0,0)"),
                    t.fillRect(0, 0, 1, 1),
                    t.canvas)),
                  this.changed();
              }),
              (e.prototype.handleImageLoad_ = function () {
                var t = this.image_;
                t.naturalWidth && t.naturalHeight
                  ? (this.state = 2)
                  : (this.state = 4),
                  this.unlistenImage_(),
                  this.changed();
              }),
              (e.prototype.load = function () {
                3 == this.state &&
                  ((this.state = 0),
                  (this.image_ = new Image()),
                  null !== this.crossOrigin_ &&
                    (this.image_.crossOrigin = this.crossOrigin_)),
                  0 == this.state &&
                    ((this.state = 1),
                    this.changed(),
                    this.tileLoadFunction_(this, this.src_),
                    (this.unlisten_ = xr(
                      this.image_,
                      this.handleImageLoad_.bind(this),
                      this.handleImageError_.bind(this)
                    )));
              }),
              (e.prototype.unlistenImage_ = function () {
                this.unlisten_ && (this.unlisten_(), (this.unlisten_ = null));
              }),
              e
            );
          })(_r),
          Er = (function () {
            function t(t, e, n, i, r, o) {
              (this.sourceProj_ = t), (this.targetProj_ = e);
              var s = {},
                a = tn(this.targetProj_, this.sourceProj_);
              (this.transformInv_ = function (t) {
                var e = t[0] + "/" + t[1];
                return s[e] || (s[e] = a(t)), s[e];
              }),
                (this.maxSourceExtent_ = i),
                (this.errorThresholdSquared_ = r * r),
                (this.triangles_ = []),
                (this.wrapsXInSource_ = !1),
                (this.canWrapXInSource_ =
                  this.sourceProj_.canWrapX() &&
                  !!i &&
                  !!this.sourceProj_.getExtent() &&
                  ke(i) == ke(this.sourceProj_.getExtent())),
                (this.sourceWorldWidth_ = this.sourceProj_.getExtent()
                  ? ke(this.sourceProj_.getExtent())
                  : null),
                (this.targetWorldWidth_ = this.targetProj_.getExtent()
                  ? ke(this.targetProj_.getExtent())
                  : null);
              var l = Ae(n),
                h = De(n),
                u = Re(n),
                c = Oe(n),
                p = this.transformInv_(l),
                f = this.transformInv_(h),
                d = this.transformInv_(u),
                g = this.transformInv_(c),
                _ =
                  10 +
                  (o
                    ? Math.max(0, Math.ceil(Ct(be(n) / (o * o * 256 * 256))))
                    : 0);
              if (
                (this.addQuad_(l, h, u, c, p, f, d, g, _), this.wrapsXInSource_)
              ) {
                var y = 1 / 0;
                this.triangles_.forEach(function (t, e, n) {
                  y = Math.min(
                    y,
                    t.source[0][0],
                    t.source[1][0],
                    t.source[2][0]
                  );
                }),
                  this.triangles_.forEach(
                    function (t) {
                      if (
                        Math.max(
                          t.source[0][0],
                          t.source[1][0],
                          t.source[2][0]
                        ) -
                          y >
                        this.sourceWorldWidth_ / 2
                      ) {
                        var e = [
                          [t.source[0][0], t.source[0][1]],
                          [t.source[1][0], t.source[1][1]],
                          [t.source[2][0], t.source[2][1]],
                        ];
                        e[0][0] - y > this.sourceWorldWidth_ / 2 &&
                          (e[0][0] -= this.sourceWorldWidth_),
                          e[1][0] - y > this.sourceWorldWidth_ / 2 &&
                            (e[1][0] -= this.sourceWorldWidth_),
                          e[2][0] - y > this.sourceWorldWidth_ / 2 &&
                            (e[2][0] -= this.sourceWorldWidth_);
                        var n = Math.min(e[0][0], e[1][0], e[2][0]);
                        Math.max(e[0][0], e[1][0], e[2][0]) - n <
                          this.sourceWorldWidth_ / 2 && (t.source = e);
                      }
                    }.bind(this)
                  );
              }
              s = {};
            }
            return (
              (t.prototype.addTriangle_ = function (t, e, n, i, r, o) {
                this.triangles_.push({ source: [i, r, o], target: [t, e, n] });
              }),
              (t.prototype.addQuad_ = function (t, e, n, i, r, o, s, a, l) {
                var h = ue([r, o, s, a]),
                  u = this.sourceWorldWidth_
                    ? ke(h) / this.sourceWorldWidth_
                    : null,
                  c = this.sourceWorldWidth_,
                  p = this.sourceProj_.canWrapX() && u > 0.5 && u < 1,
                  f = !1;
                if (
                  (l > 0 &&
                    (this.targetProj_.isGlobal() &&
                      this.targetWorldWidth_ &&
                      (f =
                        ke(ue([t, e, n, i])) / this.targetWorldWidth_ > 0.25 ||
                        f),
                    !p &&
                      this.sourceProj_.isGlobal() &&
                      u &&
                      (f = u > 0.25 || f)),
                  !(
                    !f &&
                    this.maxSourceExtent_ &&
                    isFinite(h[0]) &&
                    isFinite(h[1]) &&
                    isFinite(h[2]) &&
                    isFinite(h[3])
                  ) || je(h, this.maxSourceExtent_))
                ) {
                  var d = 0;
                  if (
                    !(
                      f ||
                      (isFinite(r[0]) &&
                        isFinite(r[1]) &&
                        isFinite(o[0]) &&
                        isFinite(o[1]) &&
                        isFinite(s[0]) &&
                        isFinite(s[1]) &&
                        isFinite(a[0]) &&
                        isFinite(a[1]))
                    )
                  )
                    if (l > 0) f = !0;
                    else if (
                      1 !=
                        (d =
                          (isFinite(r[0]) && isFinite(r[1]) ? 0 : 8) +
                          (isFinite(o[0]) && isFinite(o[1]) ? 0 : 4) +
                          (isFinite(s[0]) && isFinite(s[1]) ? 0 : 2) +
                          (isFinite(a[0]) && isFinite(a[1]) ? 0 : 1)) &&
                      2 != d &&
                      4 != d &&
                      8 != d
                    )
                      return;
                  if (l > 0) {
                    if (!f) {
                      var g = [(t[0] + n[0]) / 2, (t[1] + n[1]) / 2],
                        _ = this.transformInv_(g),
                        y = void 0;
                      y = p
                        ? (Tt(r[0], c) + Tt(s[0], c)) / 2 - Tt(_[0], c)
                        : (r[0] + s[0]) / 2 - _[0];
                      var v = (r[1] + s[1]) / 2 - _[1];
                      f = y * y + v * v > this.errorThresholdSquared_;
                    }
                    if (f) {
                      if (Math.abs(t[0] - n[0]) <= Math.abs(t[1] - n[1])) {
                        var m = [(e[0] + n[0]) / 2, (e[1] + n[1]) / 2],
                          x = this.transformInv_(m),
                          C = [(i[0] + t[0]) / 2, (i[1] + t[1]) / 2],
                          w = this.transformInv_(C);
                        this.addQuad_(t, e, m, C, r, o, x, w, l - 1),
                          this.addQuad_(C, m, n, i, w, x, s, a, l - 1);
                      } else {
                        var S = [(t[0] + e[0]) / 2, (t[1] + e[1]) / 2],
                          E = this.transformInv_(S),
                          T = [(n[0] + i[0]) / 2, (n[1] + i[1]) / 2],
                          b = this.transformInv_(T);
                        this.addQuad_(t, S, T, i, r, E, b, a, l - 1),
                          this.addQuad_(S, e, n, T, E, o, s, b, l - 1);
                      }
                      return;
                    }
                  }
                  if (p) {
                    if (!this.canWrapXInSource_) return;
                    this.wrapsXInSource_ = !0;
                  }
                  0 == (11 & d) && this.addTriangle_(t, n, i, r, s, a),
                    0 == (14 & d) && this.addTriangle_(t, n, e, r, s, o),
                    d &&
                      (0 == (13 & d) && this.addTriangle_(e, i, t, o, a, r),
                      0 == (7 & d) && this.addTriangle_(e, i, n, o, a, s));
                }
              }),
              (t.prototype.calculateSourceExtent = function () {
                var t = [1 / 0, 1 / 0, -1 / 0, -1 / 0];
                return (
                  this.triangles_.forEach(function (e, n, i) {
                    var r = e.source;
                    we(t, r[0]), we(t, r[1]), we(t, r[2]);
                  }),
                  t
                );
              }),
              (t.prototype.getTriangles = function () {
                return this.triangles_;
              }),
              t
            );
          })(),
          Tr = { imageSmoothingEnabled: !1, msImageSmoothingEnabled: !1 },
          br = { imageSmoothingEnabled: !0, msImageSmoothingEnabled: !0 };
        function Or(t, e, n, i, r) {
          t.beginPath(),
            t.moveTo(0, 0),
            t.lineTo(e, n),
            t.lineTo(i, r),
            t.closePath(),
            t.save(),
            t.clip(),
            t.fillRect(0, 0, Math.max(e, i) + 1, Math.max(n, r)),
            t.restore();
        }
        function Rr(t, e) {
          return (
            Math.abs(t[4 * e] - 210) > 2 || Math.abs(t[4 * e + 3] - 191.25) > 2
          );
        }
        function Ir(t, e, n, i) {
          var r = en(n, e, t),
            o = He(e, i, n),
            s = e.getMetersPerUnit();
          void 0 !== s && (o *= s);
          var a = t.getMetersPerUnit();
          void 0 !== a && (o /= a);
          var l = t.getExtent();
          if (!l || de(l, r)) {
            var h = He(t, o, r) / o;
            isFinite(h) && h > 0 && (o /= h);
          }
          return o;
        }
        var Pr = (function () {
            var t = function (e, n) {
              return (
                (t =
                  Object.setPrototypeOf ||
                  ({ __proto__: [] } instanceof Array &&
                    function (t, e) {
                      t.__proto__ = e;
                    }) ||
                  function (t, e) {
                    for (var n in e)
                      Object.prototype.hasOwnProperty.call(e, n) &&
                        (t[n] = e[n]);
                  }),
                t(e, n)
              );
            };
            return function (e, n) {
              if ("function" != typeof n && null !== n)
                throw new TypeError(
                  "Class extends value " +
                    String(n) +
                    " is not a constructor or null"
                );
              function i() {
                this.constructor = e;
              }
              t(e, n),
                (e.prototype =
                  null === n
                    ? Object.create(n)
                    : ((i.prototype = n.prototype), new i()));
            };
          })(),
          Mr = (function (t) {
            function e(e, n, i, r, o, s, a, l, h, u, c, p) {
              var f = t.call(this, o, 0, { interpolate: !!p }) || this;
              (f.renderEdges_ = void 0 !== c && c),
                (f.pixelRatio_ = a),
                (f.gutter_ = l),
                (f.canvas_ = null),
                (f.sourceTileGrid_ = n),
                (f.targetTileGrid_ = r),
                (f.wrappedTileCoord_ = s || o),
                (f.sourceTiles_ = []),
                (f.sourcesListenerKeys_ = null),
                (f.sourceZ_ = 0);
              var d = r.getTileCoordExtent(f.wrappedTileCoord_),
                g = f.targetTileGrid_.getExtent(),
                _ = f.sourceTileGrid_.getExtent(),
                y = g ? Le(d, g) : d;
              if (0 === be(y)) return (f.state = 4), f;
              var v = e.getExtent();
              v && (_ = _ ? Le(_, v) : v);
              var m = r.getResolution(f.wrappedTileCoord_[0]),
                x = (function (t, e, n, i) {
                  var r = Ie(n),
                    o = Ir(t, e, r, i);
                  return (
                    (!isFinite(o) || o <= 0) &&
                      Te(n, function (n) {
                        return (o = Ir(t, e, n, i)), isFinite(o) && o > 0;
                      }),
                    o
                  );
                })(e, i, y, m);
              if (!isFinite(x) || x <= 0) return (f.state = 4), f;
              var C = void 0 !== u ? u : 0.5;
              if (
                ((f.triangulation_ = new Er(e, i, y, _, x * C, m)),
                0 === f.triangulation_.getTriangles().length)
              )
                return (f.state = 4), f;
              f.sourceZ_ = n.getZForResolution(x);
              var w = f.triangulation_.calculateSourceExtent();
              if (
                (_ &&
                  (e.canWrapX()
                    ? ((w[1] = mt(w[1], _[1], _[3])),
                      (w[3] = mt(w[3], _[1], _[3])))
                    : (w = Le(w, _))),
                be(w))
              ) {
                for (
                  var S = n.getTileRangeForExtentAndZ(w, f.sourceZ_),
                    E = S.minX;
                  E <= S.maxX;
                  E++
                )
                  for (var T = S.minY; T <= S.maxY; T++) {
                    var b = h(f.sourceZ_, E, T, a);
                    b && f.sourceTiles_.push(b);
                  }
                0 === f.sourceTiles_.length && (f.state = 4);
              } else f.state = 4;
              return f;
            }
            return (
              Pr(e, t),
              (e.prototype.getImage = function () {
                return this.canvas_;
              }),
              (e.prototype.reproject_ = function () {
                var t = [];
                if (
                  (this.sourceTiles_.forEach(
                    function (e, n, i) {
                      e &&
                        2 == e.getState() &&
                        t.push({
                          extent: this.sourceTileGrid_.getTileCoordExtent(
                            e.tileCoord
                          ),
                          image: e.getImage(),
                        });
                    }.bind(this)
                  ),
                  (this.sourceTiles_.length = 0),
                  0 === t.length)
                )
                  this.state = 3;
                else {
                  var e = this.wrappedTileCoord_[0],
                    n = this.targetTileGrid_.getTileSize(e),
                    i = "number" == typeof n ? n : n[0],
                    r = "number" == typeof n ? n : n[1],
                    o = this.targetTileGrid_.getResolution(e),
                    s = this.sourceTileGrid_.getResolution(this.sourceZ_),
                    a = this.targetTileGrid_.getTileCoordExtent(
                      this.wrappedTileCoord_
                    );
                  (this.canvas_ = (function (
                    t,
                    e,
                    n,
                    i,
                    r,
                    o,
                    s,
                    a,
                    l,
                    h,
                    u,
                    c
                  ) {
                    var p = q(Math.round(n * t), Math.round(n * e));
                    if ((c || f(p, Tr), 0 === l.length)) return p.canvas;
                    function d(t) {
                      return Math.round(t * n) / n;
                    }
                    p.scale(n, n), (p.globalCompositeOperation = "lighter");
                    var g = [1 / 0, 1 / 0, -1 / 0, -1 / 0];
                    l.forEach(function (t, e, n) {
                      var i, r;
                      (i = g),
                        (r = t.extent)[0] < i[0] && (i[0] = r[0]),
                        r[2] > i[2] && (i[2] = r[2]),
                        r[1] < i[1] && (i[1] = r[1]),
                        r[3] > i[3] && (i[3] = r[3]);
                    });
                    var _ = ke(g),
                      y = Fe(g),
                      v = q(Math.round((n * _) / i), Math.round((n * y) / i));
                    c || f(v, Tr);
                    var m = n / i;
                    l.forEach(function (t, e, n) {
                      var i = t.extent[0] - g[0],
                        r = -(t.extent[3] - g[3]),
                        o = ke(t.extent),
                        s = Fe(t.extent);
                      t.image.width > 0 &&
                        t.image.height > 0 &&
                        v.drawImage(
                          t.image,
                          h,
                          h,
                          t.image.width - 2 * h,
                          t.image.height - 2 * h,
                          i * m,
                          r * m,
                          o * m,
                          s * m
                        );
                    });
                    var x = Ae(s);
                    return (
                      a.getTriangles().forEach(function (t, e, r) {
                        var s = t.source,
                          a = t.target,
                          l = s[0][0],
                          h = s[0][1],
                          u = s[1][0],
                          f = s[1][1],
                          _ = s[2][0],
                          y = s[2][1],
                          m = d((a[0][0] - x[0]) / o),
                          C = d(-(a[0][1] - x[1]) / o),
                          w = d((a[1][0] - x[0]) / o),
                          S = d(-(a[1][1] - x[1]) / o),
                          E = d((a[2][0] - x[0]) / o),
                          T = d(-(a[2][1] - x[1]) / o),
                          b = l,
                          O = h;
                        (l = 0), (h = 0);
                        var R = (function (t) {
                          for (var e = t.length, n = 0; n < e; n++) {
                            for (
                              var i = n, r = Math.abs(t[n][n]), o = n + 1;
                              o < e;
                              o++
                            ) {
                              var s = Math.abs(t[o][n]);
                              s > r && ((r = s), (i = o));
                            }
                            if (0 === r) return null;
                            var a = t[i];
                            (t[i] = t[n]), (t[n] = a);
                            for (var l = n + 1; l < e; l++)
                              for (
                                var h = -t[l][n] / t[n][n], u = n;
                                u < e + 1;
                                u++
                              )
                                n == u
                                  ? (t[l][u] = 0)
                                  : (t[l][u] += h * t[n][u]);
                          }
                          for (var c = new Array(e), p = e - 1; p >= 0; p--) {
                            c[p] = t[p][e] / t[p][p];
                            for (var f = p - 1; f >= 0; f--)
                              t[f][e] -= t[f][p] * c[p];
                          }
                          return c;
                        })([
                          [(u -= b), (f -= O), 0, 0, w - m],
                          [(_ -= b), (y -= O), 0, 0, E - m],
                          [0, 0, u, f, S - C],
                          [0, 0, _, y, T - C],
                        ]);
                        if (R) {
                          if (
                            (p.save(),
                            p.beginPath(),
                            (function () {
                              if (void 0 === Cr) {
                                var t = document
                                  .createElement("canvas")
                                  .getContext("2d");
                                (t.globalCompositeOperation = "lighter"),
                                  (t.fillStyle = "rgba(210, 0, 0, 0.75)"),
                                  Or(t, 4, 5, 4, 0),
                                  Or(t, 4, 5, 0, 5);
                                var e = t.getImageData(0, 0, 3, 3).data;
                                Cr = Rr(e, 0) || Rr(e, 4) || Rr(e, 8);
                              }
                              return Cr;
                            })() || !c)
                          ) {
                            p.moveTo(w, S);
                            for (var I = m - w, P = C - S, M = 0; M < 4; M++)
                              p.lineTo(
                                w + d(((M + 1) * I) / 4),
                                S + d((M * P) / 3)
                              ),
                                3 != M &&
                                  p.lineTo(
                                    w + d(((M + 1) * I) / 4),
                                    S + d(((M + 1) * P) / 3)
                                  );
                            p.lineTo(E, T);
                          } else p.moveTo(w, S), p.lineTo(m, C), p.lineTo(E, T);
                          p.clip(),
                            p.transform(R[0], R[2], R[1], R[3], m, C),
                            p.translate(g[0] - b, g[3] - O),
                            p.scale(i / n, -i / n),
                            p.drawImage(v.canvas, 0, 0),
                            p.restore();
                        }
                      }),
                      u &&
                        (p.save(),
                        (p.globalCompositeOperation = "source-over"),
                        (p.strokeStyle = "black"),
                        (p.lineWidth = 1),
                        a.getTriangles().forEach(function (t, e, n) {
                          var i = t.target,
                            r = (i[0][0] - x[0]) / o,
                            s = -(i[0][1] - x[1]) / o,
                            a = (i[1][0] - x[0]) / o,
                            l = -(i[1][1] - x[1]) / o,
                            h = (i[2][0] - x[0]) / o,
                            u = -(i[2][1] - x[1]) / o;
                          p.beginPath(),
                            p.moveTo(a, l),
                            p.lineTo(r, s),
                            p.lineTo(h, u),
                            p.closePath(),
                            p.stroke();
                        }),
                        p.restore()),
                      p.canvas
                    );
                  })(
                    i,
                    r,
                    this.pixelRatio_,
                    s,
                    this.sourceTileGrid_.getExtent(),
                    o,
                    a,
                    this.triangulation_,
                    t,
                    this.gutter_,
                    this.renderEdges_,
                    this.interpolate
                  )),
                    (this.state = 2);
                }
                this.changed();
              }),
              (e.prototype.load = function () {
                if (0 == this.state) {
                  (this.state = 1), this.changed();
                  var t = 0;
                  (this.sourcesListenerKeys_ = []),
                    this.sourceTiles_.forEach(
                      function (e, n, i) {
                        var r = e.getState();
                        if (0 == r || 1 == r) {
                          t++;
                          var o = O(
                            e,
                            x,
                            function (n) {
                              var i = e.getState();
                              (2 != i && 3 != i && 4 != i) ||
                                (I(o),
                                0 == --t &&
                                  (this.unlistenSources_(), this.reproject_()));
                            },
                            this
                          );
                          this.sourcesListenerKeys_.push(o);
                        }
                      }.bind(this)
                    ),
                    0 === t
                      ? setTimeout(this.reproject_.bind(this), 0)
                      : this.sourceTiles_.forEach(function (t, e, n) {
                          0 == t.getState() && t.load();
                        });
                }
              }),
              (e.prototype.unlistenSources_ = function () {
                this.sourcesListenerKeys_.forEach(I),
                  (this.sourcesListenerKeys_ = null);
              }),
              e
            );
          })(_r),
          Fr = (function () {
            function t(t, e, n, i) {
              (this.minX = t),
                (this.maxX = e),
                (this.minY = n),
                (this.maxY = i);
            }
            return (
              (t.prototype.contains = function (t) {
                return this.containsXY(t[1], t[2]);
              }),
              (t.prototype.containsTileRange = function (t) {
                return (
                  this.minX <= t.minX &&
                  t.maxX <= this.maxX &&
                  this.minY <= t.minY &&
                  t.maxY <= this.maxY
                );
              }),
              (t.prototype.containsXY = function (t, e) {
                return (
                  this.minX <= t &&
                  t <= this.maxX &&
                  this.minY <= e &&
                  e <= this.maxY
                );
              }),
              (t.prototype.equals = function (t) {
                return (
                  this.minX == t.minX &&
                  this.minY == t.minY &&
                  this.maxX == t.maxX &&
                  this.maxY == t.maxY
                );
              }),
              (t.prototype.extend = function (t) {
                t.minX < this.minX && (this.minX = t.minX),
                  t.maxX > this.maxX && (this.maxX = t.maxX),
                  t.minY < this.minY && (this.minY = t.minY),
                  t.maxY > this.maxY && (this.maxY = t.maxY);
              }),
              (t.prototype.getHeight = function () {
                return this.maxY - this.minY + 1;
              }),
              (t.prototype.getSize = function () {
                return [this.getWidth(), this.getHeight()];
              }),
              (t.prototype.getWidth = function () {
                return this.maxX - this.minX + 1;
              }),
              (t.prototype.intersects = function (t) {
                return (
                  this.minX <= t.maxX &&
                  this.maxX >= t.minX &&
                  this.minY <= t.maxY &&
                  this.maxY >= t.minY
                );
              }),
              t
            );
          })();
        function Lr(t, e, n, i, r) {
          return void 0 !== r
            ? ((r.minX = t), (r.maxX = e), (r.minY = n), (r.maxY = i), r)
            : new Fr(t, e, n, i);
        }
        var Ar = Fr;
        function Dr(t) {
          return t[0] > 0 && t[1] > 0;
        }
        function kr(t, e) {
          return Array.isArray(t)
            ? t
            : (void 0 === e ? (e = [t, t]) : ((e[0] = t), (e[1] = t)), e);
        }
        var jr = (function () {
            var t = function (e, n) {
              return (
                (t =
                  Object.setPrototypeOf ||
                  ({ __proto__: [] } instanceof Array &&
                    function (t, e) {
                      t.__proto__ = e;
                    }) ||
                  function (t, e) {
                    for (var n in e)
                      Object.prototype.hasOwnProperty.call(e, n) &&
                        (t[n] = e[n]);
                  }),
                t(e, n)
              );
            };
            return function (e, n) {
              if ("function" != typeof n && null !== n)
                throw new TypeError(
                  "Class extends value " +
                    String(n) +
                    " is not a constructor or null"
                );
              function i() {
                this.constructor = e;
              }
              t(e, n),
                (e.prototype =
                  null === n
                    ? Object.create(n)
                    : ((i.prototype = n.prototype), new i()));
            };
          })(),
          Gr = (function (t) {
            function e(e) {
              var n = t.call(this, e) || this;
              return (
                (n.extentChanged = !0),
                (n.renderedExtent_ = null),
                n.renderedPixelRatio,
                (n.renderedProjection = null),
                n.renderedRevision,
                (n.renderedTiles = []),
                (n.newTiles_ = !1),
                (n.tmpExtent = [1 / 0, 1 / 0, -1 / 0, -1 / 0]),
                (n.tmpTileRange_ = new Ar(0, 0, 0, 0)),
                n
              );
            }
            return (
              jr(e, t),
              (e.prototype.isDrawableTile = function (t) {
                var e = this.getLayer(),
                  n = t.getState(),
                  i = e.getUseInterimTilesOnError();
                return 2 == n || 4 == n || (3 == n && !i);
              }),
              (e.prototype.getTile = function (t, e, n, i) {
                var r = i.pixelRatio,
                  o = i.viewState.projection,
                  s = this.getLayer(),
                  a = s.getSource().getTile(t, e, n, r, o);
                return (
                  3 == a.getState() &&
                    (s.getUseInterimTilesOnError()
                      ? s.getPreload() > 0 && (this.newTiles_ = !0)
                      : a.setState(2)),
                  this.isDrawableTile(a) || (a = a.getInterimTile()),
                  a
                );
              }),
              (e.prototype.getData = function (t) {
                var e = this.frameState;
                if (!e) return null;
                var n = this.getLayer(),
                  i = jn(e.pixelToCoordinateTransform, t.slice()),
                  r = n.getExtent();
                if (r && !de(r, i)) return null;
                for (
                  var o = e.pixelRatio,
                    s = e.viewState.projection,
                    a = e.viewState,
                    l = n.getRenderSource(),
                    h = l.getTileGridForProjection(a.projection),
                    u = l.getTilePixelRatio(e.pixelRatio),
                    c = h.getZForResolution(a.resolution);
                  c >= h.getMinZoom();
                  --c
                ) {
                  var p = h.getTileCoordForCoordAndZ(i, c),
                    f = l.getTile(c, p[1], p[2], o, s);
                  if (!(f instanceof Sr || f instanceof Mr)) return null;
                  if (2 === f.getState()) {
                    var d = h.getOrigin(c),
                      g = kr(h.getTileSize(c)),
                      _ = h.getResolution(c),
                      y = Math.floor(u * ((i[0] - d[0]) / _ - p[1] * g[0])),
                      v = Math.floor(u * ((d[1] - i[1]) / _ - p[2] * g[1]));
                    return this.getImageData(f.getImage(), y, v);
                  }
                }
                return null;
              }),
              (e.prototype.loadedTileCallback = function (e, n, i) {
                return (
                  !!this.isDrawableTile(i) &&
                  t.prototype.loadedTileCallback.call(this, e, n, i)
                );
              }),
              (e.prototype.prepareFrame = function (t) {
                return !!this.getLayer().getSource();
              }),
              (e.prototype.renderFrame = function (t, e) {
                var n = t.layerStatesArray[t.layerIndex],
                  i = t.viewState,
                  r = i.projection,
                  s = i.resolution,
                  a = i.center,
                  l = i.rotation,
                  h = t.pixelRatio,
                  u = this.getLayer(),
                  c = u.getSource(),
                  p = c.getRevision(),
                  d = c.getTileGridForProjection(r),
                  g = d.getZForResolution(s, c.zDirection),
                  _ = d.getResolution(g),
                  y = t.extent,
                  v = n.extent && pn(n.extent);
                v && (y = Le(y, pn(n.extent)));
                var m = c.getTilePixelRatio(h),
                  x = Math.round(t.size[0] * m),
                  C = Math.round(t.size[1] * m);
                if (l) {
                  var w = Math.round(Math.sqrt(x * x + C * C));
                  (x = w), (C = w);
                }
                var S = (_ * x) / 2 / m,
                  E = (_ * C) / 2 / m,
                  T = [a[0] - S, a[1] - E, a[0] + S, a[1] + E],
                  b = d.getTileRangeForExtentAndZ(y, g),
                  O = {};
                O[g] = {};
                var R = this.createLoadedTileFinder(c, r, O),
                  I = this.tmpExtent,
                  P = this.tmpTileRange_;
                this.newTiles_ = !1;
                for (var M = b.minX; M <= b.maxX; ++M)
                  for (var F = b.minY; F <= b.maxY; ++F) {
                    var L = this.getTile(g, M, F, t);
                    if (this.isDrawableTile(L)) {
                      var A = D(this);
                      if (2 == L.getState()) {
                        O[g][L.tileCoord.toString()] = L;
                        var k = L.inTransition(A);
                        this.newTiles_ ||
                          (!k && -1 !== this.renderedTiles.indexOf(L)) ||
                          (this.newTiles_ = !0);
                      }
                      if (1 === L.getAlpha(A, t.time)) continue;
                    }
                    var j = d.getTileCoordChildTileRange(L.tileCoord, P, I),
                      G = !1;
                    j && (G = R(g + 1, j)),
                      G ||
                        d.forEachTileCoordParentTileRange(L.tileCoord, R, P, I);
                  }
                var z = _ / s;
                Gn(
                  this.pixelTransform,
                  t.size[0] / 2,
                  t.size[1] / 2,
                  1 / m,
                  1 / m,
                  l,
                  -x / 2,
                  -C / 2
                );
                var W = Wn(this.pixelTransform);
                this.useContainer(e, W, n.opacity, this.getBackground(t));
                var X = this.context,
                  N = X.canvas;
                zn(this.inversePixelTransform, this.pixelTransform),
                  Gn(this.tempTransform, x / 2, C / 2, z, z, 0, -x / 2, -C / 2),
                  N.width != x || N.height != C
                    ? ((N.width = x), (N.height = C))
                    : this.containerReused || X.clearRect(0, 0, x, C),
                  v && this.clipUnrotated(X, t, v),
                  c.getInterpolate() || f(X, Tr),
                  this.preRender(X, t),
                  (this.renderedTiles.length = 0);
                var Y,
                  B,
                  K,
                  Z = Object.keys(O).map(Number);
                Z.sort(o),
                  1 !== n.opacity ||
                  (this.containerReused && !c.getOpaque(t.viewState.projection))
                    ? ((Y = []), (B = []))
                    : (Z = Z.reverse());
                for (var V = Z.length - 1; V >= 0; --V) {
                  var U = Z[V],
                    H = c.getTilePixelSize(U, h, r),
                    q = d.getResolution(U) / _,
                    J = H[0] * q * z,
                    Q = H[1] * q * z,
                    $ = d.getTileCoordForCoordAndZ(Ae(T), U),
                    tt = d.getTileCoordExtent($),
                    et = jn(this.tempTransform, [
                      (m * (tt[0] - T[0])) / _,
                      (m * (T[3] - tt[3])) / _,
                    ]),
                    nt = m * c.getGutterForProjection(r),
                    it = O[U];
                  for (var rt in it) {
                    var ot = (L = it[rt]).tileCoord,
                      at = $[1] - ot[1],
                      lt = Math.round(et[0] - (at - 1) * J),
                      ht = $[2] - ot[2],
                      ut = Math.round(et[1] - (ht - 1) * Q),
                      ct = lt - (M = Math.round(et[0] - at * J)),
                      pt = ut - (F = Math.round(et[1] - ht * Q)),
                      ft = g === U,
                      dt = !1;
                    if (!(k = ft && 1 !== L.getAlpha(D(this), t.time)))
                      if (Y) {
                        K = [M, F, M + ct, F, M + ct, F + pt, M, F + pt];
                        for (var gt = 0, _t = Y.length; gt < _t; ++gt)
                          if (g !== U && U < B[gt]) {
                            var yt = Y[gt];
                            je(
                              [M, F, M + ct, F + pt],
                              [yt[0], yt[3], yt[4], yt[7]]
                            ) &&
                              (dt || (X.save(), (dt = !0)),
                              X.beginPath(),
                              X.moveTo(K[0], K[1]),
                              X.lineTo(K[2], K[3]),
                              X.lineTo(K[4], K[5]),
                              X.lineTo(K[6], K[7]),
                              X.moveTo(yt[6], yt[7]),
                              X.lineTo(yt[4], yt[5]),
                              X.lineTo(yt[2], yt[3]),
                              X.lineTo(yt[0], yt[1]),
                              X.clip());
                          }
                        Y.push(K), B.push(U);
                      } else X.clearRect(M, F, ct, pt);
                    this.drawTileImage(L, t, M, F, ct, pt, nt, ft),
                      Y && !k
                        ? (dt && X.restore(), this.renderedTiles.unshift(L))
                        : this.renderedTiles.push(L),
                      this.updateUsedTiles(t.usedTiles, c, L);
                  }
                }
                (this.renderedRevision = p),
                  (this.renderedResolution = _),
                  (this.extentChanged =
                    !this.renderedExtent_ || !Ce(this.renderedExtent_, T)),
                  (this.renderedExtent_ = T),
                  (this.renderedPixelRatio = h),
                  (this.renderedProjection = r),
                  this.manageTilePyramid(t, c, d, h, r, y, g, u.getPreload()),
                  this.scheduleExpireCache(t, c),
                  this.postRender(X, t),
                  n.extent && X.restore(),
                  f(X, br),
                  W !== N.style.transform && (N.style.transform = W);
                var vt = st(n.opacity),
                  mt = this.container;
                return (
                  vt !== mt.style.opacity && (mt.style.opacity = vt),
                  this.container
                );
              }),
              (e.prototype.drawTileImage = function (t, e, n, i, r, o, s, a) {
                var l = this.getTileImage(t);
                if (l) {
                  var h = D(this),
                    u = a ? t.getAlpha(h, e.time) : 1,
                    c = u !== this.context.globalAlpha;
                  c && (this.context.save(), (this.context.globalAlpha = u)),
                    this.context.drawImage(
                      l,
                      s,
                      s,
                      l.width - 2 * s,
                      l.height - 2 * s,
                      n,
                      i,
                      r,
                      o
                    ),
                    c && this.context.restore(),
                    1 !== u ? (e.animate = !0) : a && t.endTransition(h);
                }
              }),
              (e.prototype.getImage = function () {
                var t = this.context;
                return t ? t.canvas : null;
              }),
              (e.prototype.getTileImage = function (t) {
                return t.getImage();
              }),
              (e.prototype.scheduleExpireCache = function (t, e) {
                if (e.canExpireCache()) {
                  var n = function (t, e, n) {
                    var i = D(t);
                    i in n.usedTiles &&
                      t.expireCache(n.viewState.projection, n.usedTiles[i]);
                  }.bind(null, e);
                  t.postRenderFunctions.push(n);
                }
              }),
              (e.prototype.updateUsedTiles = function (t, e, n) {
                var i = D(e);
                i in t || (t[i] = {}), (t[i][n.getKey()] = !0);
              }),
              (e.prototype.manageTilePyramid = function (
                t,
                e,
                n,
                i,
                r,
                o,
                s,
                a,
                l
              ) {
                var h = D(e);
                h in t.wantedTiles || (t.wantedTiles[h] = {});
                var u,
                  c,
                  p,
                  f,
                  d,
                  g,
                  _ = t.wantedTiles[h],
                  y = t.tileQueue,
                  v = 0;
                for (g = n.getMinZoom(); g <= s; ++g)
                  for (
                    c = n.getTileRangeForExtentAndZ(o, g, c),
                      p = n.getResolution(g),
                      f = c.minX;
                    f <= c.maxX;
                    ++f
                  )
                    for (d = c.minY; d <= c.maxY; ++d)
                      s - g <= a
                        ? (++v,
                          0 == (u = e.getTile(g, f, d, i, r)).getState() &&
                            ((_[u.getKey()] = !0),
                            y.isKeyQueued(u.getKey()) ||
                              y.enqueue([
                                u,
                                h,
                                n.getTileCoordCenter(u.tileCoord),
                                p,
                              ])),
                          void 0 !== l && l(u))
                        : e.useTile(g, f, d, r);
                e.updateCacheSize(v, r);
              }),
              e
            );
          })(dr),
          zr = Gr,
          Wr = (function () {
            var t = function (e, n) {
              return (
                (t =
                  Object.setPrototypeOf ||
                  ({ __proto__: [] } instanceof Array &&
                    function (t, e) {
                      t.__proto__ = e;
                    }) ||
                  function (t, e) {
                    for (var n in e)
                      Object.prototype.hasOwnProperty.call(e, n) &&
                        (t[n] = e[n]);
                  }),
                t(e, n)
              );
            };
            return function (e, n) {
              if ("function" != typeof n && null !== n)
                throw new TypeError(
                  "Class extends value " +
                    String(n) +
                    " is not a constructor or null"
                );
              function i() {
                this.constructor = e;
              }
              t(e, n),
                (e.prototype =
                  null === n
                    ? Object.create(n)
                    : ((i.prototype = n.prototype), new i()));
            };
          })(),
          Xr = (function (t) {
            function e(e) {
              return t.call(this, e) || this;
            }
            return (
              Wr(e, t),
              (e.prototype.createRenderer = function () {
                return new zr(this);
              }),
              e
            );
          })($i),
          Nr = n(582),
          Yr = (function () {
            function t(t) {
              (this.opacity_ = t.opacity),
                (this.rotateWithView_ = t.rotateWithView),
                (this.rotation_ = t.rotation),
                (this.scale_ = t.scale),
                (this.scaleArray_ = kr(t.scale)),
                (this.displacement_ = t.displacement);
            }
            return (
              (t.prototype.clone = function () {
                var e = this.getScale();
                return new t({
                  opacity: this.getOpacity(),
                  scale: Array.isArray(e) ? e.slice() : e,
                  rotation: this.getRotation(),
                  rotateWithView: this.getRotateWithView(),
                  displacement: this.getDisplacement().slice(),
                });
              }),
              (t.prototype.getOpacity = function () {
                return this.opacity_;
              }),
              (t.prototype.getRotateWithView = function () {
                return this.rotateWithView_;
              }),
              (t.prototype.getRotation = function () {
                return this.rotation_;
              }),
              (t.prototype.getScale = function () {
                return this.scale_;
              }),
              (t.prototype.getScaleArray = function () {
                return this.scaleArray_;
              }),
              (t.prototype.getDisplacement = function () {
                return this.displacement_;
              }),
              (t.prototype.getAnchor = function () {
                return L();
              }),
              (t.prototype.getImage = function (t) {
                return L();
              }),
              (t.prototype.getHitDetectionImage = function () {
                return L();
              }),
              (t.prototype.getPixelRatio = function (t) {
                return 1;
              }),
              (t.prototype.getImageState = function () {
                return L();
              }),
              (t.prototype.getImageSize = function () {
                return L();
              }),
              (t.prototype.getOrigin = function () {
                return L();
              }),
              (t.prototype.getSize = function () {
                return L();
              }),
              (t.prototype.setDisplacement = function (t) {
                this.displacement_ = t;
              }),
              (t.prototype.setOpacity = function (t) {
                this.opacity_ = t;
              }),
              (t.prototype.setRotateWithView = function (t) {
                this.rotateWithView_ = t;
              }),
              (t.prototype.setRotation = function (t) {
                this.rotation_ = t;
              }),
              (t.prototype.setScale = function (t) {
                (this.scale_ = t), (this.scaleArray_ = kr(t));
              }),
              (t.prototype.listenImageChange = function (t) {
                L();
              }),
              (t.prototype.load = function () {
                L();
              }),
              (t.prototype.unlistenImageChange = function (t) {
                L();
              }),
              t
            );
          })();
        function Br(t) {
          return Array.isArray(t) ? ur(t) : t;
        }
        var Kr = "10px sans-serif",
          Zr = "#000",
          Vr = "round",
          Ur = [],
          Hr = "round",
          qr = "#000",
          Jr = "center",
          Qr = "middle",
          $r = [0, 0, 0, 0],
          to = new G();
        new m().setSize = function () {
          console.warn("labelCache is deprecated.");
        };
        var eo,
          no,
          io = null,
          ro = {},
          oo = (function () {
            var t,
              e,
              n = "32px ",
              i = ["monospace", "serif"],
              r = i.length,
              o = "wmytzilWMYTZIL@#/&?$%10";
            function s(t, s, a) {
              for (var l = !0, h = 0; h < r; ++h) {
                var u = i[h];
                if (((e = lo(t + " " + s + " " + n + u, o)), a != u)) {
                  var c = lo(t + " " + s + " " + n + a + "," + u, o);
                  l = l && c != e;
                }
              }
              return !!l;
            }
            function a() {
              for (
                var e = !0, n = to.getKeys(), i = 0, r = n.length;
                i < r;
                ++i
              ) {
                var o = n[i];
                to.get(o) < 100 &&
                  (s.apply(this, o.split("\n"))
                    ? (d(ro), (io = null), (eo = void 0), to.set(o, 100))
                    : (to.set(o, to.get(o) + 1, !0), (e = !1)));
              }
              e && (clearInterval(t), (t = void 0));
            }
            return function (e) {
              var n = ot(e);
              if (n)
                for (var i = n.families, r = 0, o = i.length; r < o; ++r) {
                  var l = i[r],
                    h = n.style + "\n" + n.weight + "\n" + l;
                  void 0 === to.get(h) &&
                    (to.set(h, 100, !0),
                    s(n.style, n.weight, l) ||
                      (to.set(h, 0, !0),
                      void 0 === t && (t = setInterval(a, 32))));
                }
            };
          })(),
          so = function (t) {
            var e = ro[t];
            if (null == e) {
              if (V) {
                var n = ot(t),
                  i = ao(t, "Žg");
                e =
                  (isNaN(Number(n.lineHeight)) ? 1.2 : Number(n.lineHeight)) *
                  (i.actualBoundingBoxAscent + i.actualBoundingBoxDescent);
              } else
                no ||
                  (((no = document.createElement("div")).innerHTML = "M"),
                  (no.style.minHeight = "0"),
                  (no.style.maxHeight = "none"),
                  (no.style.height = "auto"),
                  (no.style.padding = "0"),
                  (no.style.border = "none"),
                  (no.style.position = "absolute"),
                  (no.style.display = "block"),
                  (no.style.left = "-99999px")),
                  (no.style.font = t),
                  document.body.appendChild(no),
                  (e = no.offsetHeight),
                  document.body.removeChild(no);
              ro[t] = e;
            }
            return e;
          };
        function ao(t, e) {
          return (
            io || (io = q(1, 1)),
            t != eo && ((io.font = t), (eo = io.font)),
            io.measureText(e)
          );
        }
        function lo(t, e) {
          return ao(t, e).width;
        }
        function ho(t, e, n) {
          if (e in n) return n[e];
          var i = lo(t, e);
          return (n[e] = i), i;
        }
        var uo = (function () {
            var t = function (e, n) {
              return (
                (t =
                  Object.setPrototypeOf ||
                  ({ __proto__: [] } instanceof Array &&
                    function (t, e) {
                      t.__proto__ = e;
                    }) ||
                  function (t, e) {
                    for (var n in e)
                      Object.prototype.hasOwnProperty.call(e, n) &&
                        (t[n] = e[n]);
                  }),
                t(e, n)
              );
            };
            return function (e, n) {
              if ("function" != typeof n && null !== n)
                throw new TypeError(
                  "Class extends value " +
                    String(n) +
                    " is not a constructor or null"
                );
              function i() {
                this.constructor = e;
              }
              t(e, n),
                (e.prototype =
                  null === n
                    ? Object.create(n)
                    : ((i.prototype = n.prototype), new i()));
            };
          })(),
          co = (function (t) {
            function e(e) {
              var n = this,
                i = void 0 !== e.rotateWithView && e.rotateWithView;
              return (
                ((n =
                  t.call(this, {
                    opacity: 1,
                    rotateWithView: i,
                    rotation: void 0 !== e.rotation ? e.rotation : 0,
                    scale: void 0 !== e.scale ? e.scale : 1,
                    displacement:
                      void 0 !== e.displacement ? e.displacement : [0, 0],
                  }) || this).canvas_ = void 0),
                (n.hitDetectionCanvas_ = null),
                (n.fill_ = void 0 !== e.fill ? e.fill : null),
                (n.origin_ = [0, 0]),
                (n.points_ = e.points),
                (n.radius_ = void 0 !== e.radius ? e.radius : e.radius1),
                (n.radius2_ = e.radius2),
                (n.angle_ = void 0 !== e.angle ? e.angle : 0),
                (n.stroke_ = void 0 !== e.stroke ? e.stroke : null),
                (n.size_ = null),
                (n.renderOptions_ = null),
                n.render(),
                n
              );
            }
            return (
              uo(e, t),
              (e.prototype.clone = function () {
                var t = this.getScale(),
                  n = new e({
                    fill: this.getFill() ? this.getFill().clone() : void 0,
                    points: this.getPoints(),
                    radius: this.getRadius(),
                    radius2: this.getRadius2(),
                    angle: this.getAngle(),
                    stroke: this.getStroke()
                      ? this.getStroke().clone()
                      : void 0,
                    rotation: this.getRotation(),
                    rotateWithView: this.getRotateWithView(),
                    scale: Array.isArray(t) ? t.slice() : t,
                    displacement: this.getDisplacement().slice(),
                  });
                return n.setOpacity(this.getOpacity()), n;
              }),
              (e.prototype.getAnchor = function () {
                var t = this.size_;
                if (!t) return null;
                var e = this.getDisplacement();
                return [t[0] / 2 - e[0], t[1] / 2 + e[1]];
              }),
              (e.prototype.getAngle = function () {
                return this.angle_;
              }),
              (e.prototype.getFill = function () {
                return this.fill_;
              }),
              (e.prototype.getHitDetectionImage = function () {
                return (
                  this.hitDetectionCanvas_ ||
                    this.createHitDetectionCanvas_(this.renderOptions_),
                  this.hitDetectionCanvas_
                );
              }),
              (e.prototype.getImage = function (t) {
                var e = this.canvas_[t];
                if (!e) {
                  var n = this.renderOptions_,
                    i = q(n.size * t, n.size * t);
                  this.draw_(n, i, t), (e = i.canvas), (this.canvas_[t] = e);
                }
                return e;
              }),
              (e.prototype.getPixelRatio = function (t) {
                return t;
              }),
              (e.prototype.getImageSize = function () {
                return this.size_;
              }),
              (e.prototype.getImageState = function () {
                return 2;
              }),
              (e.prototype.getOrigin = function () {
                return this.origin_;
              }),
              (e.prototype.getPoints = function () {
                return this.points_;
              }),
              (e.prototype.getRadius = function () {
                return this.radius_;
              }),
              (e.prototype.getRadius2 = function () {
                return this.radius2_;
              }),
              (e.prototype.getSize = function () {
                return this.size_;
              }),
              (e.prototype.getStroke = function () {
                return this.stroke_;
              }),
              (e.prototype.listenImageChange = function (t) {}),
              (e.prototype.load = function () {}),
              (e.prototype.unlistenImageChange = function (t) {}),
              (e.prototype.calculateLineJoinSize_ = function (t, e, n) {
                if (
                  0 === e ||
                  this.points_ === 1 / 0 ||
                  ("bevel" !== t && "miter" !== t)
                )
                  return e;
                var i = this.radius_,
                  r = void 0 === this.radius2_ ? i : this.radius2_;
                if (i < r) {
                  var o = i;
                  (i = r), (r = o);
                }
                var s =
                    void 0 === this.radius2_ ? this.points_ : 2 * this.points_,
                  a = (2 * Math.PI) / s,
                  l = r * Math.sin(a),
                  h = i - Math.sqrt(r * r - l * l),
                  u = Math.sqrt(l * l + h * h),
                  c = u / l;
                if ("miter" === t && c <= n) return c * e;
                var p = e / 2 / c,
                  f = (e / 2) * (h / u),
                  d = Math.sqrt((i + p) * (i + p) + f * f) - i;
                if (void 0 === this.radius2_ || "bevel" === t) return 2 * d;
                var g = i * Math.sin(a),
                  _ = r - Math.sqrt(i * i - g * g),
                  y = Math.sqrt(g * g + _ * _) / g;
                if (y <= n) {
                  var v = (y * e) / 2 - r - i;
                  return 2 * Math.max(d, v);
                }
                return 2 * d;
              }),
              (e.prototype.createRenderOptions = function () {
                var t,
                  e = Hr,
                  n = 0,
                  i = null,
                  r = 0,
                  o = 0;
                this.stroke_ &&
                  (null === (t = this.stroke_.getColor()) && (t = qr),
                  (t = Br(t)),
                  void 0 === (o = this.stroke_.getWidth()) && (o = 1),
                  (i = this.stroke_.getLineDash()),
                  (r = this.stroke_.getLineDashOffset()),
                  void 0 === (e = this.stroke_.getLineJoin()) && (e = Hr),
                  void 0 === (n = this.stroke_.getMiterLimit()) && (n = 10));
                var s = this.calculateLineJoinSize_(e, o, n),
                  a = Math.max(this.radius_, this.radius2_ || 0);
                return {
                  strokeStyle: t,
                  strokeWidth: o,
                  size: Math.ceil(2 * a + s),
                  lineDash: i,
                  lineDashOffset: r,
                  lineJoin: e,
                  miterLimit: n,
                };
              }),
              (e.prototype.render = function () {
                this.renderOptions_ = this.createRenderOptions();
                var t = this.renderOptions_.size;
                (this.canvas_ = {}), (this.size_ = [t, t]);
              }),
              (e.prototype.draw_ = function (t, e, n) {
                if (
                  (e.scale(n, n),
                  e.translate(t.size / 2, t.size / 2),
                  this.createPath_(e),
                  this.fill_)
                ) {
                  var i = this.fill_.getColor();
                  null === i && (i = Zr), (e.fillStyle = Br(i)), e.fill();
                }
                this.stroke_ &&
                  ((e.strokeStyle = t.strokeStyle),
                  (e.lineWidth = t.strokeWidth),
                  e.setLineDash &&
                    t.lineDash &&
                    (e.setLineDash(t.lineDash),
                    (e.lineDashOffset = t.lineDashOffset)),
                  (e.lineJoin = t.lineJoin),
                  (e.miterLimit = t.miterLimit),
                  e.stroke());
              }),
              (e.prototype.createHitDetectionCanvas_ = function (t) {
                if (this.fill_) {
                  var e = this.fill_.getColor(),
                    n = 0;
                  if (
                    ("string" == typeof e && (e = lr(e)),
                    null === e
                      ? (n = 1)
                      : Array.isArray(e) && (n = 4 === e.length ? e[3] : 1),
                    0 === n)
                  ) {
                    var i = q(t.size, t.size);
                    (this.hitDetectionCanvas_ = i.canvas),
                      this.drawHitDetectionCanvas_(t, i);
                  }
                }
                this.hitDetectionCanvas_ ||
                  (this.hitDetectionCanvas_ = this.getImage(1));
              }),
              (e.prototype.createPath_ = function (t) {
                var e = this.points_,
                  n = this.radius_;
                if (e === 1 / 0) t.arc(0, 0, n, 0, 2 * Math.PI);
                else {
                  var i = void 0 === this.radius2_ ? n : this.radius2_;
                  void 0 !== this.radius2_ && (e *= 2);
                  for (
                    var r = this.angle_ - Math.PI / 2,
                      o = (2 * Math.PI) / e,
                      s = 0;
                    s < e;
                    s++
                  ) {
                    var a = r + s * o,
                      l = s % 2 == 0 ? n : i;
                    t.lineTo(l * Math.cos(a), l * Math.sin(a));
                  }
                  t.closePath();
                }
              }),
              (e.prototype.drawHitDetectionCanvas_ = function (t, e) {
                e.translate(t.size / 2, t.size / 2),
                  this.createPath_(e),
                  (e.fillStyle = Zr),
                  e.fill(),
                  this.stroke_ &&
                    ((e.strokeStyle = t.strokeStyle),
                    (e.lineWidth = t.strokeWidth),
                    t.lineDash &&
                      (e.setLineDash(t.lineDash),
                      (e.lineDashOffset = t.lineDashOffset)),
                    (e.lineJoin = t.lineJoin),
                    (e.miterLimit = t.miterLimit),
                    e.stroke());
              }),
              e
            );
          })(Yr),
          po = (function () {
            var t = function (e, n) {
              return (
                (t =
                  Object.setPrototypeOf ||
                  ({ __proto__: [] } instanceof Array &&
                    function (t, e) {
                      t.__proto__ = e;
                    }) ||
                  function (t, e) {
                    for (var n in e)
                      Object.prototype.hasOwnProperty.call(e, n) &&
                        (t[n] = e[n]);
                  }),
                t(e, n)
              );
            };
            return function (e, n) {
              if ("function" != typeof n && null !== n)
                throw new TypeError(
                  "Class extends value " +
                    String(n) +
                    " is not a constructor or null"
                );
              function i() {
                this.constructor = e;
              }
              t(e, n),
                (e.prototype =
                  null === n
                    ? Object.create(n)
                    : ((i.prototype = n.prototype), new i()));
            };
          })(),
          fo = (function (t) {
            function e(e) {
              var n = e || {};
              return (
                t.call(this, {
                  points: 1 / 0,
                  fill: n.fill,
                  radius: n.radius,
                  stroke: n.stroke,
                  scale: void 0 !== n.scale ? n.scale : 1,
                  rotation: void 0 !== n.rotation ? n.rotation : 0,
                  rotateWithView:
                    void 0 !== n.rotateWithView && n.rotateWithView,
                  displacement:
                    void 0 !== n.displacement ? n.displacement : [0, 0],
                }) || this
              );
            }
            return (
              po(e, t),
              (e.prototype.clone = function () {
                var t = this.getScale(),
                  n = new e({
                    fill: this.getFill() ? this.getFill().clone() : void 0,
                    stroke: this.getStroke()
                      ? this.getStroke().clone()
                      : void 0,
                    radius: this.getRadius(),
                    scale: Array.isArray(t) ? t.slice() : t,
                    rotation: this.getRotation(),
                    rotateWithView: this.getRotateWithView(),
                    displacement: this.getDisplacement().slice(),
                  });
                return n.setOpacity(this.getOpacity()), n;
              }),
              (e.prototype.setRadius = function (t) {
                (this.radius_ = t), this.render();
              }),
              e
            );
          })(co),
          go = (function () {
            function t(t) {
              var e = t || {};
              this.color_ = void 0 !== e.color ? e.color : null;
            }
            return (
              (t.prototype.clone = function () {
                var e = this.getColor();
                return new t({
                  color: Array.isArray(e) ? e.slice() : e || void 0,
                });
              }),
              (t.prototype.getColor = function () {
                return this.color_;
              }),
              (t.prototype.setColor = function (t) {
                this.color_ = t;
              }),
              t
            );
          })(),
          _o = (function () {
            function t(t) {
              var e = t || {};
              (this.color_ = void 0 !== e.color ? e.color : null),
                (this.lineCap_ = e.lineCap),
                (this.lineDash_ = void 0 !== e.lineDash ? e.lineDash : null),
                (this.lineDashOffset_ = e.lineDashOffset),
                (this.lineJoin_ = e.lineJoin),
                (this.miterLimit_ = e.miterLimit),
                (this.width_ = e.width);
            }
            return (
              (t.prototype.clone = function () {
                var e = this.getColor();
                return new t({
                  color: Array.isArray(e) ? e.slice() : e || void 0,
                  lineCap: this.getLineCap(),
                  lineDash: this.getLineDash()
                    ? this.getLineDash().slice()
                    : void 0,
                  lineDashOffset: this.getLineDashOffset(),
                  lineJoin: this.getLineJoin(),
                  miterLimit: this.getMiterLimit(),
                  width: this.getWidth(),
                });
              }),
              (t.prototype.getColor = function () {
                return this.color_;
              }),
              (t.prototype.getLineCap = function () {
                return this.lineCap_;
              }),
              (t.prototype.getLineDash = function () {
                return this.lineDash_;
              }),
              (t.prototype.getLineDashOffset = function () {
                return this.lineDashOffset_;
              }),
              (t.prototype.getLineJoin = function () {
                return this.lineJoin_;
              }),
              (t.prototype.getMiterLimit = function () {
                return this.miterLimit_;
              }),
              (t.prototype.getWidth = function () {
                return this.width_;
              }),
              (t.prototype.setColor = function (t) {
                this.color_ = t;
              }),
              (t.prototype.setLineCap = function (t) {
                this.lineCap_ = t;
              }),
              (t.prototype.setLineDash = function (t) {
                this.lineDash_ = t;
              }),
              (t.prototype.setLineDashOffset = function (t) {
                this.lineDashOffset_ = t;
              }),
              (t.prototype.setLineJoin = function (t) {
                this.lineJoin_ = t;
              }),
              (t.prototype.setMiterLimit = function (t) {
                this.miterLimit_ = t;
              }),
              (t.prototype.setWidth = function (t) {
                this.width_ = t;
              }),
              t
            );
          })(),
          yo = (function () {
            function t(t) {
              var e = t || {};
              (this.geometry_ = null),
                (this.geometryFunction_ = xo),
                void 0 !== e.geometry && this.setGeometry(e.geometry),
                (this.fill_ = void 0 !== e.fill ? e.fill : null),
                (this.image_ = void 0 !== e.image ? e.image : null),
                (this.renderer_ = void 0 !== e.renderer ? e.renderer : null),
                (this.hitDetectionRenderer_ =
                  void 0 !== e.hitDetectionRenderer
                    ? e.hitDetectionRenderer
                    : null),
                (this.stroke_ = void 0 !== e.stroke ? e.stroke : null),
                (this.text_ = void 0 !== e.text ? e.text : null),
                (this.zIndex_ = e.zIndex);
            }
            return (
              (t.prototype.clone = function () {
                var e = this.getGeometry();
                return (
                  e && "object" == typeof e && (e = e.clone()),
                  new t({
                    geometry: e,
                    fill: this.getFill() ? this.getFill().clone() : void 0,
                    image: this.getImage() ? this.getImage().clone() : void 0,
                    renderer: this.getRenderer(),
                    stroke: this.getStroke()
                      ? this.getStroke().clone()
                      : void 0,
                    text: this.getText() ? this.getText().clone() : void 0,
                    zIndex: this.getZIndex(),
                  })
                );
              }),
              (t.prototype.getRenderer = function () {
                return this.renderer_;
              }),
              (t.prototype.setRenderer = function (t) {
                this.renderer_ = t;
              }),
              (t.prototype.setHitDetectionRenderer = function (t) {
                this.hitDetectionRenderer_ = t;
              }),
              (t.prototype.getHitDetectionRenderer = function () {
                return this.hitDetectionRenderer_;
              }),
              (t.prototype.getGeometry = function () {
                return this.geometry_;
              }),
              (t.prototype.getGeometryFunction = function () {
                return this.geometryFunction_;
              }),
              (t.prototype.getFill = function () {
                return this.fill_;
              }),
              (t.prototype.setFill = function (t) {
                this.fill_ = t;
              }),
              (t.prototype.getImage = function () {
                return this.image_;
              }),
              (t.prototype.setImage = function (t) {
                this.image_ = t;
              }),
              (t.prototype.getStroke = function () {
                return this.stroke_;
              }),
              (t.prototype.setStroke = function (t) {
                this.stroke_ = t;
              }),
              (t.prototype.getText = function () {
                return this.text_;
              }),
              (t.prototype.setText = function (t) {
                this.text_ = t;
              }),
              (t.prototype.getZIndex = function () {
                return this.zIndex_;
              }),
              (t.prototype.setGeometry = function (t) {
                "function" == typeof t
                  ? (this.geometryFunction_ = t)
                  : "string" == typeof t
                  ? (this.geometryFunction_ = function (e) {
                      return e.get(t);
                    })
                  : t
                  ? void 0 !== t &&
                    (this.geometryFunction_ = function () {
                      return t;
                    })
                  : (this.geometryFunction_ = xo),
                  (this.geometry_ = t);
              }),
              (t.prototype.setZIndex = function (t) {
                this.zIndex_ = t;
              }),
              t
            );
          })(),
          vo = null;
        function mo(t, e) {
          if (!vo) {
            var n = new go({ color: "rgba(255,255,255,0.4)" }),
              i = new _o({ color: "#3399CC", width: 1.25 });
            vo = [
              new yo({
                image: new fo({ fill: n, stroke: i, radius: 5 }),
                fill: n,
                stroke: i,
              }),
            ];
          }
          return vo;
        }
        function xo(t) {
          return t.getGeometry();
        }
        var Co = yo,
          wo = (function () {
            var t = function (e, n) {
              return (
                (t =
                  Object.setPrototypeOf ||
                  ({ __proto__: [] } instanceof Array &&
                    function (t, e) {
                      t.__proto__ = e;
                    }) ||
                  function (t, e) {
                    for (var n in e)
                      Object.prototype.hasOwnProperty.call(e, n) &&
                        (t[n] = e[n]);
                  }),
                t(e, n)
              );
            };
            return function (e, n) {
              if ("function" != typeof n && null !== n)
                throw new TypeError(
                  "Class extends value " +
                    String(n) +
                    " is not a constructor or null"
                );
              function i() {
                this.constructor = e;
              }
              t(e, n),
                (e.prototype =
                  null === n
                    ? Object.create(n)
                    : ((i.prototype = n.prototype), new i()));
            };
          })(),
          So = "renderOrder",
          Eo = (function (t) {
            function e(e) {
              var n = this,
                i = e || {},
                r = f({}, i);
              return (
                delete r.style,
                delete r.renderBuffer,
                delete r.updateWhileAnimating,
                delete r.updateWhileInteracting,
                ((n = t.call(this, r) || this).declutter_ =
                  void 0 !== i.declutter && i.declutter),
                (n.renderBuffer_ =
                  void 0 !== i.renderBuffer ? i.renderBuffer : 100),
                (n.style_ = null),
                (n.styleFunction_ = void 0),
                n.setStyle(i.style),
                (n.updateWhileAnimating_ =
                  void 0 !== i.updateWhileAnimating && i.updateWhileAnimating),
                (n.updateWhileInteracting_ =
                  void 0 !== i.updateWhileInteracting &&
                  i.updateWhileInteracting),
                n
              );
            }
            return (
              wo(e, t),
              (e.prototype.getDeclutter = function () {
                return this.declutter_;
              }),
              (e.prototype.getFeatures = function (e) {
                return t.prototype.getFeatures.call(this, e);
              }),
              (e.prototype.getRenderBuffer = function () {
                return this.renderBuffer_;
              }),
              (e.prototype.getRenderOrder = function () {
                return this.get(So);
              }),
              (e.prototype.getStyle = function () {
                return this.style_;
              }),
              (e.prototype.getStyleFunction = function () {
                return this.styleFunction_;
              }),
              (e.prototype.getUpdateWhileAnimating = function () {
                return this.updateWhileAnimating_;
              }),
              (e.prototype.getUpdateWhileInteracting = function () {
                return this.updateWhileInteracting_;
              }),
              (e.prototype.renderDeclutter = function (t) {
                t.declutterTree || (t.declutterTree = new Nr(9)),
                  this.getRenderer().renderDeclutter(t);
              }),
              (e.prototype.setRenderOrder = function (t) {
                this.set(So, t);
              }),
              (e.prototype.setStyle = function (t) {
                (this.style_ = void 0 !== t ? t : mo),
                  (this.styleFunction_ =
                    null === t
                      ? void 0
                      : (function (t) {
                          var e, n;
                          "function" == typeof t
                            ? (e = t)
                            : (Array.isArray(t)
                                ? (n = t)
                                : (vt("function" == typeof t.getZIndex, 41),
                                  (n = [t])),
                              (e = function () {
                                return n;
                              }));
                          return e;
                        })(this.style_)),
                  this.changed();
              }),
              e
            );
          })(Gt),
          To = {
            BEGIN_GEOMETRY: 0,
            BEGIN_PATH: 1,
            CIRCLE: 2,
            CLOSE_PATH: 3,
            CUSTOM: 4,
            DRAW_CHARS: 5,
            DRAW_IMAGE: 6,
            END_GEOMETRY: 7,
            FILL: 8,
            MOVE_TO_LINE_TO: 9,
            SET_FILL_STYLE: 10,
            SET_STROKE_STYLE: 11,
            STROKE: 12,
          },
          bo = [To.FILL],
          Oo = [To.STROKE],
          Ro = [To.BEGIN_PATH],
          Io = [To.CLOSE_PATH],
          Po = To,
          Mo = (function () {
            function t() {}
            return (
              (t.prototype.drawCustom = function (t, e, n, i) {}),
              (t.prototype.drawGeometry = function (t) {}),
              (t.prototype.setStyle = function (t) {}),
              (t.prototype.drawCircle = function (t, e) {}),
              (t.prototype.drawFeature = function (t, e) {}),
              (t.prototype.drawGeometryCollection = function (t, e) {}),
              (t.prototype.drawLineString = function (t, e) {}),
              (t.prototype.drawMultiLineString = function (t, e) {}),
              (t.prototype.drawMultiPoint = function (t, e) {}),
              (t.prototype.drawMultiPolygon = function (t, e) {}),
              (t.prototype.drawPoint = function (t, e) {}),
              (t.prototype.drawPolygon = function (t, e) {}),
              (t.prototype.drawText = function (t, e) {}),
              (t.prototype.setFillStrokeStyle = function (t, e) {}),
              (t.prototype.setImageStyle = function (t, e) {}),
              (t.prototype.setTextStyle = function (t, e) {}),
              t
            );
          })(),
          Fo = (function () {
            var t = function (e, n) {
              return (
                (t =
                  Object.setPrototypeOf ||
                  ({ __proto__: [] } instanceof Array &&
                    function (t, e) {
                      t.__proto__ = e;
                    }) ||
                  function (t, e) {
                    for (var n in e)
                      Object.prototype.hasOwnProperty.call(e, n) &&
                        (t[n] = e[n]);
                  }),
                t(e, n)
              );
            };
            return function (e, n) {
              if ("function" != typeof n && null !== n)
                throw new TypeError(
                  "Class extends value " +
                    String(n) +
                    " is not a constructor or null"
                );
              function i() {
                this.constructor = e;
              }
              t(e, n),
                (e.prototype =
                  null === n
                    ? Object.create(n)
                    : ((i.prototype = n.prototype), new i()));
            };
          })(),
          Lo = (function (t) {
            function e(e, n, i, r) {
              var o = t.call(this) || this;
              return (
                (o.tolerance = e),
                (o.maxExtent = n),
                (o.pixelRatio = r),
                (o.maxLineWidth = 0),
                (o.resolution = i),
                (o.beginGeometryInstruction1_ = null),
                (o.beginGeometryInstruction2_ = null),
                (o.bufferedMaxExtent_ = null),
                (o.instructions = []),
                (o.coordinates = []),
                (o.tmpCoordinate_ = []),
                (o.hitDetectionInstructions = []),
                (o.state = {}),
                o
              );
            }
            return (
              Fo(e, t),
              (e.prototype.applyPixelRatio = function (t) {
                var e = this.pixelRatio;
                return 1 == e
                  ? t
                  : t.map(function (t) {
                      return t * e;
                    });
              }),
              (e.prototype.appendFlatPointCoordinates = function (t, e) {
                for (
                  var n = this.getBufferedMaxExtent(),
                    i = this.tmpCoordinate_,
                    r = this.coordinates,
                    o = r.length,
                    s = 0,
                    a = t.length;
                  s < a;
                  s += e
                )
                  (i[0] = t[s]),
                    (i[1] = t[s + 1]),
                    de(n, i) && ((r[o++] = i[0]), (r[o++] = i[1]));
                return o;
              }),
              (e.prototype.appendFlatLineCoordinates = function (
                t,
                e,
                n,
                i,
                r,
                o
              ) {
                var s = this.coordinates,
                  a = s.length,
                  l = this.getBufferedMaxExtent();
                o && (e += i);
                var h,
                  u,
                  c,
                  p = t[e],
                  f = t[e + 1],
                  d = this.tmpCoordinate_,
                  g = !0;
                for (h = e + i; h < n; h += i)
                  (d[0] = t[h]),
                    (d[1] = t[h + 1]),
                    (c = ye(l, d)) !== u
                      ? (g && ((s[a++] = p), (s[a++] = f), (g = !1)),
                        (s[a++] = d[0]),
                        (s[a++] = d[1]))
                      : 1 === c
                      ? ((s[a++] = d[0]), (s[a++] = d[1]), (g = !1))
                      : (g = !0),
                    (p = d[0]),
                    (f = d[1]),
                    (u = c);
                return (
                  ((r && g) || h === e + i) && ((s[a++] = p), (s[a++] = f)), a
                );
              }),
              (e.prototype.drawCustomCoordinates_ = function (t, e, n, i, r) {
                for (var o = 0, s = n.length; o < s; ++o) {
                  var a = n[o],
                    l = this.appendFlatLineCoordinates(t, e, a, i, !1, !1);
                  r.push(l), (e = a);
                }
                return e;
              }),
              (e.prototype.drawCustom = function (t, e, n, i) {
                this.beginGeometry(t, e);
                var r,
                  o,
                  s,
                  a,
                  l,
                  h = t.getType(),
                  u = t.getStride(),
                  c = this.coordinates.length;
                switch (h) {
                  case An:
                    (r = t.getOrientedFlatCoordinates()), (a = []);
                    var p = t.getEndss();
                    l = 0;
                    for (var f = 0, d = p.length; f < d; ++f) {
                      var g = [];
                      (l = this.drawCustomCoordinates_(r, l, p[f], u, g)),
                        a.push(g);
                    }
                    this.instructions.push([Po.CUSTOM, c, a, t, n, ci]),
                      this.hitDetectionInstructions.push([
                        Po.CUSTOM,
                        c,
                        a,
                        t,
                        i || n,
                        ci,
                      ]);
                    break;
                  case Mn:
                  case Ln:
                    (s = []),
                      (r =
                        h == Mn
                          ? t.getOrientedFlatCoordinates()
                          : t.getFlatCoordinates()),
                      (l = this.drawCustomCoordinates_(
                        r,
                        0,
                        t.getEnds(),
                        u,
                        s
                      )),
                      this.instructions.push([Po.CUSTOM, c, s, t, n, ui]),
                      this.hitDetectionInstructions.push([
                        Po.CUSTOM,
                        c,
                        s,
                        t,
                        i || n,
                        ui,
                      ]);
                    break;
                  case Pn:
                  case kn:
                    (r = t.getFlatCoordinates()),
                      (o = this.appendFlatLineCoordinates(
                        r,
                        0,
                        r.length,
                        u,
                        !1,
                        !1
                      )),
                      this.instructions.push([Po.CUSTOM, c, o, t, n, hi]),
                      this.hitDetectionInstructions.push([
                        Po.CUSTOM,
                        c,
                        o,
                        t,
                        i || n,
                        hi,
                      ]);
                    break;
                  case Fn:
                    (r = t.getFlatCoordinates()),
                      (o = this.appendFlatPointCoordinates(r, u)) > c &&
                        (this.instructions.push([Po.CUSTOM, c, o, t, n, hi]),
                        this.hitDetectionInstructions.push([
                          Po.CUSTOM,
                          c,
                          o,
                          t,
                          i || n,
                          hi,
                        ]));
                    break;
                  case In:
                    (r = t.getFlatCoordinates()),
                      this.coordinates.push(r[0], r[1]),
                      (o = this.coordinates.length),
                      this.instructions.push([Po.CUSTOM, c, o, t, n]),
                      this.hitDetectionInstructions.push([
                        Po.CUSTOM,
                        c,
                        o,
                        t,
                        i || n,
                      ]);
                }
                this.endGeometry(e);
              }),
              (e.prototype.beginGeometry = function (t, e) {
                (this.beginGeometryInstruction1_ = [
                  Po.BEGIN_GEOMETRY,
                  e,
                  0,
                  t,
                ]),
                  this.instructions.push(this.beginGeometryInstruction1_),
                  (this.beginGeometryInstruction2_ = [
                    Po.BEGIN_GEOMETRY,
                    e,
                    0,
                    t,
                  ]),
                  this.hitDetectionInstructions.push(
                    this.beginGeometryInstruction2_
                  );
              }),
              (e.prototype.finish = function () {
                return {
                  instructions: this.instructions,
                  hitDetectionInstructions: this.hitDetectionInstructions,
                  coordinates: this.coordinates,
                };
              }),
              (e.prototype.reverseHitDetectionInstructions = function () {
                var t,
                  e = this.hitDetectionInstructions;
                e.reverse();
                var n,
                  i,
                  r = e.length,
                  o = -1;
                for (t = 0; t < r; ++t)
                  (i = (n = e[t])[0]) == Po.END_GEOMETRY
                    ? (o = t)
                    : i == Po.BEGIN_GEOMETRY &&
                      ((n[2] = t),
                      a(this.hitDetectionInstructions, o, t),
                      (o = -1));
              }),
              (e.prototype.setFillStrokeStyle = function (t, e) {
                var n = this.state;
                if (t) {
                  var i = t.getColor();
                  n.fillStyle = Br(i || Zr);
                } else n.fillStyle = void 0;
                if (e) {
                  var r = e.getColor();
                  n.strokeStyle = Br(r || qr);
                  var o = e.getLineCap();
                  n.lineCap = void 0 !== o ? o : Vr;
                  var s = e.getLineDash();
                  n.lineDash = s ? s.slice() : Ur;
                  var a = e.getLineDashOffset();
                  n.lineDashOffset = a || 0;
                  var l = e.getLineJoin();
                  n.lineJoin = void 0 !== l ? l : Hr;
                  var h = e.getWidth();
                  n.lineWidth = void 0 !== h ? h : 1;
                  var u = e.getMiterLimit();
                  (n.miterLimit = void 0 !== u ? u : 10),
                    n.lineWidth > this.maxLineWidth &&
                      ((this.maxLineWidth = n.lineWidth),
                      (this.bufferedMaxExtent_ = null));
                } else
                  (n.strokeStyle = void 0),
                    (n.lineCap = void 0),
                    (n.lineDash = null),
                    (n.lineDashOffset = void 0),
                    (n.lineJoin = void 0),
                    (n.lineWidth = void 0),
                    (n.miterLimit = void 0);
              }),
              (e.prototype.createFill = function (t) {
                var e = t.fillStyle,
                  n = [Po.SET_FILL_STYLE, e];
                return "string" != typeof e && n.push(!0), n;
              }),
              (e.prototype.applyStroke = function (t) {
                this.instructions.push(this.createStroke(t));
              }),
              (e.prototype.createStroke = function (t) {
                return [
                  Po.SET_STROKE_STYLE,
                  t.strokeStyle,
                  t.lineWidth * this.pixelRatio,
                  t.lineCap,
                  t.lineJoin,
                  t.miterLimit,
                  this.applyPixelRatio(t.lineDash),
                  t.lineDashOffset * this.pixelRatio,
                ];
              }),
              (e.prototype.updateFillStyle = function (t, e) {
                var n = t.fillStyle;
                ("string" == typeof n && t.currentFillStyle == n) ||
                  (void 0 !== n && this.instructions.push(e.call(this, t)),
                  (t.currentFillStyle = n));
              }),
              (e.prototype.updateStrokeStyle = function (t, e) {
                var n = t.strokeStyle,
                  i = t.lineCap,
                  r = t.lineDash,
                  o = t.lineDashOffset,
                  s = t.lineJoin,
                  a = t.lineWidth,
                  l = t.miterLimit;
                (t.currentStrokeStyle != n ||
                  t.currentLineCap != i ||
                  (r != t.currentLineDash && !h(t.currentLineDash, r)) ||
                  t.currentLineDashOffset != o ||
                  t.currentLineJoin != s ||
                  t.currentLineWidth != a ||
                  t.currentMiterLimit != l) &&
                  (void 0 !== n && e.call(this, t),
                  (t.currentStrokeStyle = n),
                  (t.currentLineCap = i),
                  (t.currentLineDash = r),
                  (t.currentLineDashOffset = o),
                  (t.currentLineJoin = s),
                  (t.currentLineWidth = a),
                  (t.currentMiterLimit = l));
              }),
              (e.prototype.endGeometry = function (t) {
                (this.beginGeometryInstruction1_[2] = this.instructions.length),
                  (this.beginGeometryInstruction1_ = null),
                  (this.beginGeometryInstruction2_[2] =
                    this.hitDetectionInstructions.length),
                  (this.beginGeometryInstruction2_ = null);
                var e = [Po.END_GEOMETRY, t];
                this.instructions.push(e),
                  this.hitDetectionInstructions.push(e);
              }),
              (e.prototype.getBufferedMaxExtent = function () {
                if (
                  !this.bufferedMaxExtent_ &&
                  ((this.bufferedMaxExtent_ = pe(this.maxExtent)),
                  this.maxLineWidth > 0)
                ) {
                  var t = (this.resolution * (this.maxLineWidth + 1)) / 2;
                  ce(this.bufferedMaxExtent_, t, this.bufferedMaxExtent_);
                }
                return this.bufferedMaxExtent_;
              }),
              e
            );
          })(Mo),
          Ao = Lo,
          Do = (function () {
            var t = function (e, n) {
              return (
                (t =
                  Object.setPrototypeOf ||
                  ({ __proto__: [] } instanceof Array &&
                    function (t, e) {
                      t.__proto__ = e;
                    }) ||
                  function (t, e) {
                    for (var n in e)
                      Object.prototype.hasOwnProperty.call(e, n) &&
                        (t[n] = e[n]);
                  }),
                t(e, n)
              );
            };
            return function (e, n) {
              if ("function" != typeof n && null !== n)
                throw new TypeError(
                  "Class extends value " +
                    String(n) +
                    " is not a constructor or null"
                );
              function i() {
                this.constructor = e;
              }
              t(e, n),
                (e.prototype =
                  null === n
                    ? Object.create(n)
                    : ((i.prototype = n.prototype), new i()));
            };
          })(),
          ko = (function (t) {
            function e(e, n, i, r) {
              var o = t.call(this, e, n, i, r) || this;
              return (
                (o.hitDetectionImage_ = null),
                (o.image_ = null),
                (o.imagePixelRatio_ = void 0),
                (o.anchorX_ = void 0),
                (o.anchorY_ = void 0),
                (o.height_ = void 0),
                (o.opacity_ = void 0),
                (o.originX_ = void 0),
                (o.originY_ = void 0),
                (o.rotateWithView_ = void 0),
                (o.rotation_ = void 0),
                (o.scale_ = void 0),
                (o.width_ = void 0),
                (o.declutterImageWithText_ = void 0),
                o
              );
            }
            return (
              Do(e, t),
              (e.prototype.drawPoint = function (t, e) {
                if (this.image_) {
                  this.beginGeometry(t, e);
                  var n = t.getFlatCoordinates(),
                    i = t.getStride(),
                    r = this.coordinates.length,
                    o = this.appendFlatPointCoordinates(n, i);
                  this.instructions.push([
                    Po.DRAW_IMAGE,
                    r,
                    o,
                    this.image_,
                    this.anchorX_ * this.imagePixelRatio_,
                    this.anchorY_ * this.imagePixelRatio_,
                    Math.ceil(this.height_ * this.imagePixelRatio_),
                    this.opacity_,
                    this.originX_,
                    this.originY_,
                    this.rotateWithView_,
                    this.rotation_,
                    [
                      (this.scale_[0] * this.pixelRatio) /
                        this.imagePixelRatio_,
                      (this.scale_[1] * this.pixelRatio) /
                        this.imagePixelRatio_,
                    ],
                    Math.ceil(this.width_ * this.imagePixelRatio_),
                    this.declutterImageWithText_,
                  ]),
                    this.hitDetectionInstructions.push([
                      Po.DRAW_IMAGE,
                      r,
                      o,
                      this.hitDetectionImage_,
                      this.anchorX_,
                      this.anchorY_,
                      this.height_,
                      this.opacity_,
                      this.originX_,
                      this.originY_,
                      this.rotateWithView_,
                      this.rotation_,
                      this.scale_,
                      this.width_,
                      this.declutterImageWithText_,
                    ]),
                    this.endGeometry(e);
                }
              }),
              (e.prototype.drawMultiPoint = function (t, e) {
                if (this.image_) {
                  this.beginGeometry(t, e);
                  var n = t.getFlatCoordinates(),
                    i = t.getStride(),
                    r = this.coordinates.length,
                    o = this.appendFlatPointCoordinates(n, i);
                  this.instructions.push([
                    Po.DRAW_IMAGE,
                    r,
                    o,
                    this.image_,
                    this.anchorX_ * this.imagePixelRatio_,
                    this.anchorY_ * this.imagePixelRatio_,
                    Math.ceil(this.height_ * this.imagePixelRatio_),
                    this.opacity_,
                    this.originX_,
                    this.originY_,
                    this.rotateWithView_,
                    this.rotation_,
                    [
                      (this.scale_[0] * this.pixelRatio) /
                        this.imagePixelRatio_,
                      (this.scale_[1] * this.pixelRatio) /
                        this.imagePixelRatio_,
                    ],
                    Math.ceil(this.width_ * this.imagePixelRatio_),
                    this.declutterImageWithText_,
                  ]),
                    this.hitDetectionInstructions.push([
                      Po.DRAW_IMAGE,
                      r,
                      o,
                      this.hitDetectionImage_,
                      this.anchorX_,
                      this.anchorY_,
                      this.height_,
                      this.opacity_,
                      this.originX_,
                      this.originY_,
                      this.rotateWithView_,
                      this.rotation_,
                      this.scale_,
                      this.width_,
                      this.declutterImageWithText_,
                    ]),
                    this.endGeometry(e);
                }
              }),
              (e.prototype.finish = function () {
                return (
                  this.reverseHitDetectionInstructions(),
                  (this.anchorX_ = void 0),
                  (this.anchorY_ = void 0),
                  (this.hitDetectionImage_ = null),
                  (this.image_ = null),
                  (this.imagePixelRatio_ = void 0),
                  (this.height_ = void 0),
                  (this.scale_ = void 0),
                  (this.opacity_ = void 0),
                  (this.originX_ = void 0),
                  (this.originY_ = void 0),
                  (this.rotateWithView_ = void 0),
                  (this.rotation_ = void 0),
                  (this.width_ = void 0),
                  t.prototype.finish.call(this)
                );
              }),
              (e.prototype.setImageStyle = function (t, e) {
                var n = t.getAnchor(),
                  i = t.getSize(),
                  r = t.getHitDetectionImage(),
                  o = t.getImage(this.pixelRatio),
                  s = t.getOrigin();
                (this.imagePixelRatio_ = t.getPixelRatio(this.pixelRatio)),
                  (this.anchorX_ = n[0]),
                  (this.anchorY_ = n[1]),
                  (this.hitDetectionImage_ = r),
                  (this.image_ = o),
                  (this.height_ = i[1]),
                  (this.opacity_ = t.getOpacity()),
                  (this.originX_ = s[0] * this.imagePixelRatio_),
                  (this.originY_ = s[1] * this.imagePixelRatio_),
                  (this.rotateWithView_ = t.getRotateWithView()),
                  (this.rotation_ = t.getRotation()),
                  (this.scale_ = t.getScaleArray()),
                  (this.width_ = i[0]),
                  (this.declutterImageWithText_ = e);
              }),
              e
            );
          })(Ao),
          jo = (function () {
            var t = function (e, n) {
              return (
                (t =
                  Object.setPrototypeOf ||
                  ({ __proto__: [] } instanceof Array &&
                    function (t, e) {
                      t.__proto__ = e;
                    }) ||
                  function (t, e) {
                    for (var n in e)
                      Object.prototype.hasOwnProperty.call(e, n) &&
                        (t[n] = e[n]);
                  }),
                t(e, n)
              );
            };
            return function (e, n) {
              if ("function" != typeof n && null !== n)
                throw new TypeError(
                  "Class extends value " +
                    String(n) +
                    " is not a constructor or null"
                );
              function i() {
                this.constructor = e;
              }
              t(e, n),
                (e.prototype =
                  null === n
                    ? Object.create(n)
                    : ((i.prototype = n.prototype), new i()));
            };
          })(),
          Go = (function (t) {
            function e(e, n, i, r) {
              return t.call(this, e, n, i, r) || this;
            }
            return (
              jo(e, t),
              (e.prototype.drawFlatCoordinates_ = function (t, e, n, i) {
                var r = this.coordinates.length,
                  o = this.appendFlatLineCoordinates(t, e, n, i, !1, !1),
                  s = [Po.MOVE_TO_LINE_TO, r, o];
                return (
                  this.instructions.push(s),
                  this.hitDetectionInstructions.push(s),
                  n
                );
              }),
              (e.prototype.drawLineString = function (t, e) {
                var n = this.state,
                  i = n.strokeStyle,
                  r = n.lineWidth;
                if (void 0 !== i && void 0 !== r) {
                  this.updateStrokeStyle(n, this.applyStroke),
                    this.beginGeometry(t, e),
                    this.hitDetectionInstructions.push(
                      [
                        Po.SET_STROKE_STYLE,
                        n.strokeStyle,
                        n.lineWidth,
                        n.lineCap,
                        n.lineJoin,
                        n.miterLimit,
                        Ur,
                        0,
                      ],
                      Ro
                    );
                  var o = t.getFlatCoordinates(),
                    s = t.getStride();
                  this.drawFlatCoordinates_(o, 0, o.length, s),
                    this.hitDetectionInstructions.push(Oo),
                    this.endGeometry(e);
                }
              }),
              (e.prototype.drawMultiLineString = function (t, e) {
                var n = this.state,
                  i = n.strokeStyle,
                  r = n.lineWidth;
                if (void 0 !== i && void 0 !== r) {
                  this.updateStrokeStyle(n, this.applyStroke),
                    this.beginGeometry(t, e),
                    this.hitDetectionInstructions.push(
                      [
                        Po.SET_STROKE_STYLE,
                        n.strokeStyle,
                        n.lineWidth,
                        n.lineCap,
                        n.lineJoin,
                        n.miterLimit,
                        n.lineDash,
                        n.lineDashOffset,
                      ],
                      Ro
                    );
                  for (
                    var o = t.getEnds(),
                      s = t.getFlatCoordinates(),
                      a = t.getStride(),
                      l = 0,
                      h = 0,
                      u = o.length;
                    h < u;
                    ++h
                  )
                    l = this.drawFlatCoordinates_(s, l, o[h], a);
                  this.hitDetectionInstructions.push(Oo), this.endGeometry(e);
                }
              }),
              (e.prototype.finish = function () {
                var e = this.state;
                return (
                  null != e.lastStroke &&
                    e.lastStroke != this.coordinates.length &&
                    this.instructions.push(Oo),
                  this.reverseHitDetectionInstructions(),
                  (this.state = null),
                  t.prototype.finish.call(this)
                );
              }),
              (e.prototype.applyStroke = function (e) {
                null != e.lastStroke &&
                  e.lastStroke != this.coordinates.length &&
                  (this.instructions.push(Oo),
                  (e.lastStroke = this.coordinates.length)),
                  (e.lastStroke = 0),
                  t.prototype.applyStroke.call(this, e),
                  this.instructions.push(Ro);
              }),
              e
            );
          })(Ao),
          zo = (function () {
            var t = function (e, n) {
              return (
                (t =
                  Object.setPrototypeOf ||
                  ({ __proto__: [] } instanceof Array &&
                    function (t, e) {
                      t.__proto__ = e;
                    }) ||
                  function (t, e) {
                    for (var n in e)
                      Object.prototype.hasOwnProperty.call(e, n) &&
                        (t[n] = e[n]);
                  }),
                t(e, n)
              );
            };
            return function (e, n) {
              if ("function" != typeof n && null !== n)
                throw new TypeError(
                  "Class extends value " +
                    String(n) +
                    " is not a constructor or null"
                );
              function i() {
                this.constructor = e;
              }
              t(e, n),
                (e.prototype =
                  null === n
                    ? Object.create(n)
                    : ((i.prototype = n.prototype), new i()));
            };
          })(),
          Wo = (function (t) {
            function e(e, n, i, r) {
              return t.call(this, e, n, i, r) || this;
            }
            return (
              zo(e, t),
              (e.prototype.drawFlatCoordinatess_ = function (t, e, n, i) {
                var r = this.state,
                  o = void 0 !== r.fillStyle,
                  s = void 0 !== r.strokeStyle,
                  a = n.length;
                this.instructions.push(Ro),
                  this.hitDetectionInstructions.push(Ro);
                for (var l = 0; l < a; ++l) {
                  var h = n[l],
                    u = this.coordinates.length,
                    c = this.appendFlatLineCoordinates(t, e, h, i, !0, !s),
                    p = [Po.MOVE_TO_LINE_TO, u, c];
                  this.instructions.push(p),
                    this.hitDetectionInstructions.push(p),
                    s &&
                      (this.instructions.push(Io),
                      this.hitDetectionInstructions.push(Io)),
                    (e = h);
                }
                return (
                  o &&
                    (this.instructions.push(bo),
                    this.hitDetectionInstructions.push(bo)),
                  s &&
                    (this.instructions.push(Oo),
                    this.hitDetectionInstructions.push(Oo)),
                  e
                );
              }),
              (e.prototype.drawCircle = function (t, e) {
                var n = this.state,
                  i = n.fillStyle,
                  r = n.strokeStyle;
                if (void 0 !== i || void 0 !== r) {
                  this.setFillStrokeStyles_(),
                    this.beginGeometry(t, e),
                    void 0 !== n.fillStyle &&
                      this.hitDetectionInstructions.push([
                        Po.SET_FILL_STYLE,
                        Zr,
                      ]),
                    void 0 !== n.strokeStyle &&
                      this.hitDetectionInstructions.push([
                        Po.SET_STROKE_STYLE,
                        n.strokeStyle,
                        n.lineWidth,
                        n.lineCap,
                        n.lineJoin,
                        n.miterLimit,
                        n.lineDash,
                        n.lineDashOffset,
                      ]);
                  var o = t.getFlatCoordinates(),
                    s = t.getStride(),
                    a = this.coordinates.length;
                  this.appendFlatLineCoordinates(o, 0, o.length, s, !1, !1);
                  var l = [Po.CIRCLE, a];
                  this.instructions.push(Ro, l),
                    this.hitDetectionInstructions.push(Ro, l),
                    void 0 !== n.fillStyle &&
                      (this.instructions.push(bo),
                      this.hitDetectionInstructions.push(bo)),
                    void 0 !== n.strokeStyle &&
                      (this.instructions.push(Oo),
                      this.hitDetectionInstructions.push(Oo)),
                    this.endGeometry(e);
                }
              }),
              (e.prototype.drawPolygon = function (t, e) {
                var n = this.state,
                  i = n.fillStyle,
                  r = n.strokeStyle;
                if (void 0 !== i || void 0 !== r) {
                  this.setFillStrokeStyles_(),
                    this.beginGeometry(t, e),
                    void 0 !== n.fillStyle &&
                      this.hitDetectionInstructions.push([
                        Po.SET_FILL_STYLE,
                        Zr,
                      ]),
                    void 0 !== n.strokeStyle &&
                      this.hitDetectionInstructions.push([
                        Po.SET_STROKE_STYLE,
                        n.strokeStyle,
                        n.lineWidth,
                        n.lineCap,
                        n.lineJoin,
                        n.miterLimit,
                        n.lineDash,
                        n.lineDashOffset,
                      ]);
                  var o = t.getEnds(),
                    s = t.getOrientedFlatCoordinates(),
                    a = t.getStride();
                  this.drawFlatCoordinatess_(s, 0, o, a), this.endGeometry(e);
                }
              }),
              (e.prototype.drawMultiPolygon = function (t, e) {
                var n = this.state,
                  i = n.fillStyle,
                  r = n.strokeStyle;
                if (void 0 !== i || void 0 !== r) {
                  this.setFillStrokeStyles_(),
                    this.beginGeometry(t, e),
                    void 0 !== n.fillStyle &&
                      this.hitDetectionInstructions.push([
                        Po.SET_FILL_STYLE,
                        Zr,
                      ]),
                    void 0 !== n.strokeStyle &&
                      this.hitDetectionInstructions.push([
                        Po.SET_STROKE_STYLE,
                        n.strokeStyle,
                        n.lineWidth,
                        n.lineCap,
                        n.lineJoin,
                        n.miterLimit,
                        n.lineDash,
                        n.lineDashOffset,
                      ]);
                  for (
                    var o = t.getEndss(),
                      s = t.getOrientedFlatCoordinates(),
                      a = t.getStride(),
                      l = 0,
                      h = 0,
                      u = o.length;
                    h < u;
                    ++h
                  )
                    l = this.drawFlatCoordinatess_(s, l, o[h], a);
                  this.endGeometry(e);
                }
              }),
              (e.prototype.finish = function () {
                this.reverseHitDetectionInstructions(), (this.state = null);
                var e = this.tolerance;
                if (0 !== e)
                  for (
                    var n = this.coordinates, i = 0, r = n.length;
                    i < r;
                    ++i
                  )
                    n[i] = oi(n[i], e);
                return t.prototype.finish.call(this);
              }),
              (e.prototype.setFillStrokeStyles_ = function () {
                var t = this.state;
                void 0 !== t.fillStyle &&
                  this.updateFillStyle(t, this.createFill),
                  void 0 !== t.strokeStyle &&
                    this.updateStrokeStyle(t, this.applyStroke);
              }),
              e
            );
          })(Ao),
          Xo = Wo;
        function No(t, e, n, i, r) {
          var o,
            s,
            a,
            l,
            h,
            u,
            c,
            p,
            f,
            d = n,
            g = n,
            _ = 0,
            y = 0,
            v = n;
          for (o = n; o < i; o += r) {
            var m = e[o],
              x = e[o + 1];
            void 0 !== l &&
              ((p = m - l),
              (f = x - h),
              (a = Math.sqrt(p * p + f * f)),
              void 0 !== u &&
                ((y += s),
                Math.acos((u * p + c * f) / (s * a)) > t &&
                  (y > _ && ((_ = y), (d = v), (g = o)), (y = 0), (v = o - r))),
              (s = a),
              (u = p),
              (c = f)),
              (l = m),
              (h = x);
          }
          return (y += a) > _ ? [v, o] : [d, g];
        }
        var Yo = (function () {
            var t = function (e, n) {
              return (
                (t =
                  Object.setPrototypeOf ||
                  ({ __proto__: [] } instanceof Array &&
                    function (t, e) {
                      t.__proto__ = e;
                    }) ||
                  function (t, e) {
                    for (var n in e)
                      Object.prototype.hasOwnProperty.call(e, n) &&
                        (t[n] = e[n]);
                  }),
                t(e, n)
              );
            };
            return function (e, n) {
              if ("function" != typeof n && null !== n)
                throw new TypeError(
                  "Class extends value " +
                    String(n) +
                    " is not a constructor or null"
                );
              function i() {
                this.constructor = e;
              }
              t(e, n),
                (e.prototype =
                  null === n
                    ? Object.create(n)
                    : ((i.prototype = n.prototype), new i()));
            };
          })(),
          Bo = {
            left: 0,
            end: 0,
            center: 0.5,
            right: 1,
            start: 1,
            top: 0,
            middle: 0.5,
            hanging: 0.2,
            alphabetic: 0.8,
            ideographic: 0.8,
            bottom: 1,
          },
          Ko = (function (t) {
            function e(e, n, i, r) {
              var o = t.call(this, e, n, i, r) || this;
              return (
                (o.labels_ = null),
                (o.text_ = ""),
                (o.textOffsetX_ = 0),
                (o.textOffsetY_ = 0),
                (o.textRotateWithView_ = void 0),
                (o.textRotation_ = 0),
                (o.textFillState_ = null),
                (o.fillStates = {}),
                (o.textStrokeState_ = null),
                (o.strokeStates = {}),
                (o.textState_ = {}),
                (o.textStates = {}),
                (o.textKey_ = ""),
                (o.fillKey_ = ""),
                (o.strokeKey_ = ""),
                (o.declutterImageWithText_ = void 0),
                o
              );
            }
            return (
              Yo(e, t),
              (e.prototype.finish = function () {
                var e = t.prototype.finish.call(this);
                return (
                  (e.textStates = this.textStates),
                  (e.fillStates = this.fillStates),
                  (e.strokeStates = this.strokeStates),
                  e
                );
              }),
              (e.prototype.drawText = function (t, e) {
                var n = this.textFillState_,
                  i = this.textStrokeState_,
                  r = this.textState_;
                if ("" !== this.text_ && r && (n || i)) {
                  var o = this.coordinates,
                    s = o.length,
                    a = t.getType(),
                    l = null,
                    h = t.getStride();
                  if (
                    "line" !== r.placement ||
                    (a != Pn && a != Ln && a != Mn && a != An)
                  ) {
                    var u = r.overflow ? null : [];
                    switch (a) {
                      case In:
                      case Fn:
                        l = t.getFlatCoordinates();
                        break;
                      case Pn:
                        l = t.getFlatMidpoint();
                        break;
                      case kn:
                        l = t.getCenter();
                        break;
                      case Ln:
                        (l = t.getFlatMidpoints()), (h = 2);
                        break;
                      case Mn:
                        (l = t.getFlatInteriorPoint()),
                          r.overflow || u.push(l[2] / this.resolution),
                          (h = 3);
                        break;
                      case An:
                        var c = t.getFlatInteriorPoints();
                        for (l = [], w = 0, S = c.length; w < S; w += 3)
                          r.overflow || u.push(c[w + 2] / this.resolution),
                            l.push(c[w], c[w + 1]);
                        if (0 === l.length) return;
                        h = 2;
                    }
                    if ((P = this.appendFlatPointCoordinates(l, h)) === s)
                      return;
                    if (u && (P - s) / 2 != l.length / h) {
                      var p = s / 2;
                      u = u.filter(function (t, e) {
                        var n =
                          o[2 * (p + e)] === l[e * h] &&
                          o[2 * (p + e) + 1] === l[e * h + 1];
                        return n || --p, n;
                      });
                    }
                    this.saveTextStates_(),
                      (r.backgroundFill || r.backgroundStroke) &&
                        (this.setFillStrokeStyle(
                          r.backgroundFill,
                          r.backgroundStroke
                        ),
                        r.backgroundFill &&
                          (this.updateFillStyle(this.state, this.createFill),
                          this.hitDetectionInstructions.push(
                            this.createFill(this.state)
                          )),
                        r.backgroundStroke &&
                          (this.updateStrokeStyle(this.state, this.applyStroke),
                          this.hitDetectionInstructions.push(
                            this.createStroke(this.state)
                          ))),
                      this.beginGeometry(t, e);
                    var f = r.padding;
                    if (f != $r && (r.scale[0] < 0 || r.scale[1] < 0)) {
                      var d = r.padding[0],
                        g = r.padding[1],
                        _ = r.padding[2],
                        y = r.padding[3];
                      r.scale[0] < 0 && ((g = -g), (y = -y)),
                        r.scale[1] < 0 && ((d = -d), (_ = -_)),
                        (f = [d, g, _, y]);
                    }
                    var v = this.pixelRatio;
                    this.instructions.push([
                      Po.DRAW_IMAGE,
                      s,
                      P,
                      null,
                      NaN,
                      NaN,
                      NaN,
                      1,
                      0,
                      0,
                      this.textRotateWithView_,
                      this.textRotation_,
                      [1, 1],
                      NaN,
                      this.declutterImageWithText_,
                      f == $r
                        ? $r
                        : f.map(function (t) {
                            return t * v;
                          }),
                      !!r.backgroundFill,
                      !!r.backgroundStroke,
                      this.text_,
                      this.textKey_,
                      this.strokeKey_,
                      this.fillKey_,
                      this.textOffsetX_,
                      this.textOffsetY_,
                      u,
                    ]);
                    var m = 1 / v;
                    this.hitDetectionInstructions.push([
                      Po.DRAW_IMAGE,
                      s,
                      P,
                      null,
                      NaN,
                      NaN,
                      NaN,
                      1,
                      0,
                      0,
                      this.textRotateWithView_,
                      this.textRotation_,
                      [m, m],
                      NaN,
                      this.declutterImageWithText_,
                      f,
                      !!r.backgroundFill,
                      !!r.backgroundStroke,
                      this.text_,
                      this.textKey_,
                      this.strokeKey_,
                      this.fillKey_,
                      this.textOffsetX_,
                      this.textOffsetY_,
                      u,
                    ]),
                      this.endGeometry(e);
                  } else {
                    if (!je(this.getBufferedMaxExtent(), t.getExtent())) return;
                    var x = void 0;
                    if (((l = t.getFlatCoordinates()), a == Pn)) x = [l.length];
                    else if (a == Ln) x = t.getEnds();
                    else if (a == Mn) x = t.getEnds().slice(0, 1);
                    else if (a == An) {
                      var C = t.getEndss();
                      x = [];
                      for (var w = 0, S = C.length; w < S; ++w) x.push(C[w][0]);
                    }
                    this.beginGeometry(t, e);
                    for (
                      var E = r.textAlign,
                        T = 0,
                        b = void 0,
                        O = 0,
                        R = x.length;
                      O < R;
                      ++O
                    ) {
                      if (null == E) {
                        var I = No(r.maxAngle, l, T, x[O], h);
                        (T = I[0]), (b = I[1]);
                      } else b = x[O];
                      for (w = T; w < b; w += h) o.push(l[w], l[w + 1]);
                      var P = o.length;
                      (T = x[O]), this.drawChars_(s, P), (s = P);
                    }
                    this.endGeometry(e);
                  }
                }
              }),
              (e.prototype.saveTextStates_ = function () {
                var t = this.textStrokeState_,
                  e = this.textState_,
                  n = this.textFillState_,
                  i = this.strokeKey_;
                t &&
                  (i in this.strokeStates ||
                    (this.strokeStates[i] = {
                      strokeStyle: t.strokeStyle,
                      lineCap: t.lineCap,
                      lineDashOffset: t.lineDashOffset,
                      lineWidth: t.lineWidth,
                      lineJoin: t.lineJoin,
                      miterLimit: t.miterLimit,
                      lineDash: t.lineDash,
                    }));
                var r = this.textKey_;
                r in this.textStates ||
                  (this.textStates[r] = {
                    font: e.font,
                    textAlign: e.textAlign || Jr,
                    textBaseline: e.textBaseline || Qr,
                    scale: e.scale,
                  });
                var o = this.fillKey_;
                n &&
                  (o in this.fillStates ||
                    (this.fillStates[o] = { fillStyle: n.fillStyle }));
              }),
              (e.prototype.drawChars_ = function (t, e) {
                var n = this.textStrokeState_,
                  i = this.textState_,
                  r = this.strokeKey_,
                  o = this.textKey_,
                  s = this.fillKey_;
                this.saveTextStates_();
                var a = this.pixelRatio,
                  l = Bo[i.textBaseline],
                  h = this.textOffsetY_ * a,
                  u = this.text_,
                  c = n ? (n.lineWidth * Math.abs(i.scale[0])) / 2 : 0;
                this.instructions.push([
                  Po.DRAW_CHARS,
                  t,
                  e,
                  l,
                  i.overflow,
                  s,
                  i.maxAngle,
                  a,
                  h,
                  r,
                  c * a,
                  u,
                  o,
                  1,
                ]),
                  this.hitDetectionInstructions.push([
                    Po.DRAW_CHARS,
                    t,
                    e,
                    l,
                    i.overflow,
                    s,
                    i.maxAngle,
                    1,
                    h,
                    r,
                    c,
                    u,
                    o,
                    1 / a,
                  ]);
              }),
              (e.prototype.setTextStyle = function (t, e) {
                var n, i, r;
                if (t) {
                  var o = t.getFill();
                  o
                    ? ((i = this.textFillState_) ||
                        ((i = {}), (this.textFillState_ = i)),
                      (i.fillStyle = Br(o.getColor() || Zr)))
                    : ((i = null), (this.textFillState_ = i));
                  var s = t.getStroke();
                  if (s) {
                    (r = this.textStrokeState_) ||
                      ((r = {}), (this.textStrokeState_ = r));
                    var a = s.getLineDash(),
                      l = s.getLineDashOffset(),
                      h = s.getWidth(),
                      u = s.getMiterLimit();
                    (r.lineCap = s.getLineCap() || Vr),
                      (r.lineDash = a ? a.slice() : Ur),
                      (r.lineDashOffset = void 0 === l ? 0 : l),
                      (r.lineJoin = s.getLineJoin() || Hr),
                      (r.lineWidth = void 0 === h ? 1 : h),
                      (r.miterLimit = void 0 === u ? 10 : u),
                      (r.strokeStyle = Br(s.getColor() || qr));
                  } else (r = null), (this.textStrokeState_ = r);
                  n = this.textState_;
                  var c = t.getFont() || Kr;
                  oo(c);
                  var p = t.getScaleArray();
                  (n.overflow = t.getOverflow()),
                    (n.font = c),
                    (n.maxAngle = t.getMaxAngle()),
                    (n.placement = t.getPlacement()),
                    (n.textAlign = t.getTextAlign()),
                    (n.textBaseline = t.getTextBaseline() || Qr),
                    (n.backgroundFill = t.getBackgroundFill()),
                    (n.backgroundStroke = t.getBackgroundStroke()),
                    (n.padding = t.getPadding() || $r),
                    (n.scale = void 0 === p ? [1, 1] : p);
                  var f = t.getOffsetX(),
                    d = t.getOffsetY(),
                    g = t.getRotateWithView(),
                    _ = t.getRotation();
                  (this.text_ = t.getText() || ""),
                    (this.textOffsetX_ = void 0 === f ? 0 : f),
                    (this.textOffsetY_ = void 0 === d ? 0 : d),
                    (this.textRotateWithView_ = void 0 !== g && g),
                    (this.textRotation_ = void 0 === _ ? 0 : _),
                    (this.strokeKey_ = r
                      ? ("string" == typeof r.strokeStyle
                          ? r.strokeStyle
                          : D(r.strokeStyle)) +
                        r.lineCap +
                        r.lineDashOffset +
                        "|" +
                        r.lineWidth +
                        r.lineJoin +
                        r.miterLimit +
                        "[" +
                        r.lineDash.join() +
                        "]"
                      : ""),
                    (this.textKey_ =
                      n.font +
                      n.scale +
                      (n.textAlign || "?") +
                      (n.textBaseline || "?")),
                    (this.fillKey_ = i
                      ? "string" == typeof i.fillStyle
                        ? i.fillStyle
                        : "|" + D(i.fillStyle)
                      : "");
                } else this.text_ = "";
                this.declutterImageWithText_ = e;
              }),
              e
            );
          })(Ao),
          Zo = {
            Circle: Xo,
            Default: Ao,
            Image: ko,
            LineString: Go,
            Polygon: Xo,
            Text: Ko,
          },
          Vo = (function () {
            function t(t, e, n, i) {
              (this.tolerance_ = t),
                (this.maxExtent_ = e),
                (this.pixelRatio_ = i),
                (this.resolution_ = n),
                (this.buildersByZIndex_ = {});
            }
            return (
              (t.prototype.finish = function () {
                var t = {};
                for (var e in this.buildersByZIndex_) {
                  t[e] = t[e] || {};
                  var n = this.buildersByZIndex_[e];
                  for (var i in n) {
                    var r = n[i].finish();
                    t[e][i] = r;
                  }
                }
                return t;
              }),
              (t.prototype.getBuilder = function (t, e) {
                var n = void 0 !== t ? t.toString() : "0",
                  i = this.buildersByZIndex_[n];
                void 0 === i && ((i = {}), (this.buildersByZIndex_[n] = i));
                var r = i[e];
                return (
                  void 0 === r &&
                    ((r = new (0, Zo[e])(
                      this.tolerance_,
                      this.maxExtent_,
                      this.resolution_,
                      this.pixelRatio_
                    )),
                    (i[e] = r)),
                  r
                );
              }),
              t
            );
          })(),
          Uo = "Circle",
          Ho = "Default",
          qo = "Image",
          Jo = "LineString",
          Qo = "Polygon",
          $o = "Text";
        function ts(t, e, n, i, r, o, s, a, l, h, u, c) {
          var p = t[e],
            f = t[e + 1],
            d = 0,
            g = 0,
            _ = 0,
            y = 0;
          function v() {
            (d = p),
              (g = f),
              (p = t[(e += i)]),
              (f = t[e + 1]),
              (y += _),
              (_ = Math.sqrt((p - d) * (p - d) + (f - g) * (f - g)));
          }
          do {
            v();
          } while (e < n - i && y + _ < o);
          for (
            var m = 0 === _ ? 0 : (o - y) / _,
              x = bt(d, p, m),
              C = bt(g, f, m),
              w = e - i,
              S = y,
              E = o + a * l(h, r, u);
            e < n - i && y + _ < E;

          )
            v();
          var T,
            b = bt(d, p, (m = 0 === _ ? 0 : (E - y) / _)),
            O = bt(g, f, m);
          if (c) {
            var R = [x, C, b, O];
            Nn(R, 0, 4, 2, c, R, R), (T = R[0] > R[2]);
          } else T = x > b;
          var I,
            P = Math.PI,
            M = [],
            F = w + i === e;
          if (((_ = 0), (y = S), (p = t[(e = w)]), (f = t[e + 1]), F)) {
            v(), (I = Math.atan2(f - g, p - d)), T && (I += I > 0 ? -P : P);
            var L = (b + x) / 2,
              A = (O + C) / 2;
            return (M[0] = [L, A, (E - o) / 2, I, r]), M;
          }
          for (var D = 0, k = r.length; D < k; ) {
            v();
            var j = Math.atan2(f - g, p - d);
            if ((T && (j += j > 0 ? -P : P), void 0 !== I)) {
              var G = j - I;
              if (((G += G > P ? -2 * P : G < -P ? 2 * P : 0), Math.abs(G) > s))
                return null;
            }
            I = j;
            for (var z = D, W = 0; D < k; ++D) {
              var X = a * l(h, r[T ? k - D - 1 : D], u);
              if (e + i < n && y + _ < o + W + X / 2) break;
              W += X;
            }
            if (D !== z) {
              var N = T ? r.substring(k - z, k - D) : r.substring(z, D);
              (L = bt(d, p, (m = 0 === _ ? 0 : (o + W / 2 - y) / _))),
                (A = bt(g, f, m)),
                M.push([L, A, W / 2, j, N]),
                (o += W);
            }
          }
          return M;
        }
        var es = [1 / 0, 1 / 0, -1 / 0, -1 / 0],
          ns = [],
          is = [],
          rs = [],
          os = [];
        function ss(t) {
          return t[3].declutterBox;
        }
        var as = new RegExp(
          "[" +
            String.fromCharCode(1425) +
            "-" +
            String.fromCharCode(2303) +
            String.fromCharCode(64285) +
            "-" +
            String.fromCharCode(65023) +
            String.fromCharCode(65136) +
            "-" +
            String.fromCharCode(65276) +
            String.fromCharCode(67584) +
            "-" +
            String.fromCharCode(69631) +
            String.fromCharCode(124928) +
            "-" +
            String.fromCharCode(126975) +
            "]"
        );
        function ls(t, e) {
          return (
            ("start" !== e && "end" !== e) ||
              as.test(t) ||
              (e = "start" === e ? "left" : "right"),
            Bo[e]
          );
        }
        function hs(t, e, n) {
          return n > 0 && t.push("\n", ""), t.push(e, ""), t;
        }
        var us = (function () {
            function t(t, e, n, i) {
              (this.overlaps = n),
                (this.pixelRatio = e),
                (this.resolution = t),
                this.alignFill_,
                (this.instructions = i.instructions),
                (this.coordinates = i.coordinates),
                (this.coordinateCache_ = {}),
                (this.renderedTransform_ = [1, 0, 0, 1, 0, 0]),
                (this.hitDetectionInstructions = i.hitDetectionInstructions),
                (this.pixelCoordinates_ = null),
                (this.viewRotation_ = 0),
                (this.fillStates = i.fillStates || {}),
                (this.strokeStates = i.strokeStates || {}),
                (this.textStates = i.textStates || {}),
                (this.widths_ = {}),
                (this.labels_ = {});
            }
            return (
              (t.prototype.createLabel = function (t, e, n, i) {
                var r = t + e + n + i;
                if (this.labels_[r]) return this.labels_[r];
                var o = i ? this.strokeStates[i] : null,
                  s = n ? this.fillStates[n] : null,
                  a = this.textStates[e],
                  l = this.pixelRatio,
                  h = [a.scale[0] * l, a.scale[1] * l],
                  u = Array.isArray(t),
                  c = ls(u ? t[0] : t, a.textAlign || Jr),
                  p = i && o.lineWidth ? o.lineWidth : 0,
                  f = u ? t : t.split("\n").reduce(hs, []),
                  d = (function (t, e) {
                    for (
                      var n = [],
                        i = [],
                        r = [],
                        o = 0,
                        s = 0,
                        a = 0,
                        l = 0,
                        h = 0,
                        u = e.length;
                      h <= u;
                      h += 2
                    ) {
                      var c = e[h];
                      if ("\n" !== c && h !== u) {
                        var p = e[h + 1] || t.font,
                          f = lo(p, c);
                        n.push(f), (s += f);
                        var d = so(p);
                        i.push(d), (l = Math.max(l, d));
                      } else (o = Math.max(o, s)), r.push(s), (s = 0), (a += l);
                    }
                    return {
                      width: o,
                      height: a,
                      widths: n,
                      heights: i,
                      lineWidths: r,
                    };
                  })(a, f),
                  g = d.width,
                  _ = d.height,
                  y = d.widths,
                  v = d.heights,
                  m = d.lineWidths,
                  x = g + p,
                  C = [],
                  w = (x + 2) * h[0],
                  S = (_ + p) * h[1],
                  E = {
                    width: w < 0 ? Math.floor(w) : Math.ceil(w),
                    height: S < 0 ? Math.floor(S) : Math.ceil(S),
                    contextInstructions: C,
                  };
                (1 == h[0] && 1 == h[1]) || C.push("scale", h),
                  i &&
                    (C.push("strokeStyle", o.strokeStyle),
                    C.push("lineWidth", p),
                    C.push("lineCap", o.lineCap),
                    C.push("lineJoin", o.lineJoin),
                    C.push("miterLimit", o.miterLimit),
                    (V
                      ? OffscreenCanvasRenderingContext2D
                      : CanvasRenderingContext2D
                    ).prototype.setLineDash &&
                      (C.push("setLineDash", [o.lineDash]),
                      C.push("lineDashOffset", o.lineDashOffset))),
                  n && C.push("fillStyle", s.fillStyle),
                  C.push("textBaseline", "middle"),
                  C.push("textAlign", "center");
                for (
                  var T,
                    b = 0.5 - c,
                    O = c * x + b * p,
                    R = [],
                    I = [],
                    P = 0,
                    M = 0,
                    F = 0,
                    L = 0,
                    A = 0,
                    D = f.length;
                  A < D;
                  A += 2
                ) {
                  var k = f[A];
                  if ("\n" !== k) {
                    var j = f[A + 1] || a.font;
                    j !== T &&
                      (i && R.push("font", j), n && I.push("font", j), (T = j)),
                      (P = Math.max(P, v[F]));
                    var G = [
                      k,
                      O + b * y[F] + c * (y[F] - m[L]),
                      0.5 * (p + P) + M,
                    ];
                    (O += y[F]),
                      i && R.push("strokeText", G),
                      n && I.push("fillText", G),
                      ++F;
                  } else (M += P), (P = 0), (O = c * x + b * p), ++L;
                }
                return (
                  Array.prototype.push.apply(C, R),
                  Array.prototype.push.apply(C, I),
                  (this.labels_[r] = E),
                  E
                );
              }),
              (t.prototype.replayTextBackground_ = function (
                t,
                e,
                n,
                i,
                r,
                o,
                s
              ) {
                t.beginPath(),
                  t.moveTo.apply(t, e),
                  t.lineTo.apply(t, n),
                  t.lineTo.apply(t, i),
                  t.lineTo.apply(t, r),
                  t.lineTo.apply(t, e),
                  o && ((this.alignFill_ = o[2]), this.fill_(t)),
                  s && (this.setStrokeStyle_(t, s), t.stroke());
              }),
              (t.prototype.calculateImageOrLabelDimensions_ = function (
                t,
                e,
                n,
                i,
                r,
                o,
                s,
                a,
                l,
                h,
                u,
                c,
                p,
                f,
                d,
                g
              ) {
                var _,
                  y = n - (s *= c[0]),
                  v = i - (a *= c[1]),
                  m = r + l > t ? t - l : r,
                  x = o + h > e ? e - h : o,
                  C = f[3] + m * c[0] + f[1],
                  w = f[0] + x * c[1] + f[2],
                  S = y - f[3],
                  E = v - f[0];
                return (
                  (d || 0 !== u) &&
                    ((ns[0] = S),
                    (os[0] = S),
                    (ns[1] = E),
                    (is[1] = E),
                    (is[0] = S + C),
                    (rs[0] = is[0]),
                    (rs[1] = E + w),
                    (os[1] = rs[1])),
                  0 !== u
                    ? (jn(
                        (_ = Gn([1, 0, 0, 1, 0, 0], n, i, 1, 1, u, -n, -i)),
                        ns
                      ),
                      jn(_, is),
                      jn(_, rs),
                      jn(_, os),
                      ve(
                        Math.min(ns[0], is[0], rs[0], os[0]),
                        Math.min(ns[1], is[1], rs[1], os[1]),
                        Math.max(ns[0], is[0], rs[0], os[0]),
                        Math.max(ns[1], is[1], rs[1], os[1]),
                        es
                      ))
                    : ve(
                        Math.min(S, S + C),
                        Math.min(E, E + w),
                        Math.max(S, S + C),
                        Math.max(E, E + w),
                        es
                      ),
                  p && ((y = Math.round(y)), (v = Math.round(v))),
                  {
                    drawImageX: y,
                    drawImageY: v,
                    drawImageW: m,
                    drawImageH: x,
                    originX: l,
                    originY: h,
                    declutterBox: {
                      minX: es[0],
                      minY: es[1],
                      maxX: es[2],
                      maxY: es[3],
                      value: g,
                    },
                    canvasTransform: _,
                    scale: c,
                  }
                );
              }),
              (t.prototype.replayImageOrLabel_ = function (
                t,
                e,
                n,
                i,
                r,
                o,
                s
              ) {
                var a = !(!o && !s),
                  l = i.declutterBox,
                  h = t.canvas,
                  u = s ? (s[2] * i.scale[0]) / 2 : 0;
                return (
                  l.minX - u <= h.width / e &&
                    l.maxX + u >= 0 &&
                    l.minY - u <= h.height / e &&
                    l.maxY + u >= 0 &&
                    (a && this.replayTextBackground_(t, ns, is, rs, os, o, s),
                    (function (t, e, n, i, r, o, s, a, l, h, u) {
                      t.save(),
                        1 !== n && (t.globalAlpha *= n),
                        e && t.setTransform.apply(t, e),
                        i.contextInstructions
                          ? (t.translate(l, h),
                            t.scale(u[0], u[1]),
                            (function (t, e) {
                              for (
                                var n = t.contextInstructions,
                                  i = 0,
                                  r = n.length;
                                i < r;
                                i += 2
                              )
                                Array.isArray(n[i + 1])
                                  ? e[n[i]].apply(e, n[i + 1])
                                  : (e[n[i]] = n[i + 1]);
                            })(i, t))
                          : u[0] < 0 || u[1] < 0
                          ? (t.translate(l, h),
                            t.scale(u[0], u[1]),
                            t.drawImage(i, r, o, s, a, 0, 0, s, a))
                          : t.drawImage(
                              i,
                              r,
                              o,
                              s,
                              a,
                              l,
                              h,
                              s * u[0],
                              a * u[1]
                            ),
                        t.restore();
                    })(
                      t,
                      i.canvasTransform,
                      r,
                      n,
                      i.originX,
                      i.originY,
                      i.drawImageW,
                      i.drawImageH,
                      i.drawImageX,
                      i.drawImageY,
                      i.scale
                    )),
                  !0
                );
              }),
              (t.prototype.fill_ = function (t) {
                if (this.alignFill_) {
                  var e = jn(this.renderedTransform_, [0, 0]),
                    n = 512 * this.pixelRatio;
                  t.save(),
                    t.translate(e[0] % n, e[1] % n),
                    t.rotate(this.viewRotation_);
                }
                t.fill(), this.alignFill_ && t.restore();
              }),
              (t.prototype.setStrokeStyle_ = function (t, e) {
                (t.strokeStyle = e[1]),
                  (t.lineWidth = e[2]),
                  (t.lineCap = e[3]),
                  (t.lineJoin = e[4]),
                  (t.miterLimit = e[5]),
                  t.setLineDash &&
                    ((t.lineDashOffset = e[7]), t.setLineDash(e[6]));
              }),
              (t.prototype.drawLabelWithPointPlacement_ = function (
                t,
                e,
                n,
                i
              ) {
                var r = this.textStates[e],
                  o = this.createLabel(t, e, i, n),
                  s = this.strokeStates[n],
                  a = this.pixelRatio,
                  l = ls(Array.isArray(t) ? t[0] : t, r.textAlign || Jr),
                  h = Bo[r.textBaseline || Qr],
                  u = s && s.lineWidth ? s.lineWidth : 0;
                return {
                  label: o,
                  anchorX:
                    l * (o.width / a - 2 * r.scale[0]) + 2 * (0.5 - l) * u,
                  anchorY: (h * o.height) / a + 2 * (0.5 - h) * u,
                };
              }),
              (t.prototype.execute_ = function (t, e, n, i, r, o, s, a) {
                var l, u, c;
                this.pixelCoordinates_ && h(n, this.renderedTransform_)
                  ? (l = this.pixelCoordinates_)
                  : (this.pixelCoordinates_ || (this.pixelCoordinates_ = []),
                    (l = Xn(
                      this.coordinates,
                      0,
                      this.coordinates.length,
                      2,
                      n,
                      this.pixelCoordinates_
                    )),
                    (c = n),
                    ((u = this.renderedTransform_)[0] = c[0]),
                    (u[1] = c[1]),
                    (u[2] = c[2]),
                    (u[3] = c[3]),
                    (u[4] = c[4]),
                    (u[5] = c[5]));
                for (
                  var p,
                    f,
                    d,
                    g,
                    _,
                    y,
                    v,
                    m,
                    x,
                    C,
                    w,
                    S,
                    E,
                    T,
                    b,
                    O,
                    R = 0,
                    I = i.length,
                    P = 0,
                    M = 0,
                    F = 0,
                    L = null,
                    A = null,
                    D = this.coordinateCache_,
                    k = this.viewRotation_,
                    j = Math.round(1e12 * Math.atan2(-n[1], n[0])) / 1e12,
                    G = {
                      context: t,
                      pixelRatio: this.pixelRatio,
                      resolution: this.resolution,
                      rotation: k,
                    },
                    z = this.instructions != i || this.overlaps ? 0 : 200;
                  R < I;

                ) {
                  var W = i[R];
                  switch (W[0]) {
                    case Po.BEGIN_GEOMETRY:
                      (E = W[1]),
                        (O = W[3]),
                        E.getGeometry()
                          ? void 0 === s || je(s, O.getExtent())
                            ? ++R
                            : (R = W[2] + 1)
                          : (R = W[2]);
                      break;
                    case Po.BEGIN_PATH:
                      M > z && (this.fill_(t), (M = 0)),
                        F > z && (t.stroke(), (F = 0)),
                        M || F || (t.beginPath(), (g = NaN), (_ = NaN)),
                        ++R;
                      break;
                    case Po.CIRCLE:
                      var X = l[(P = W[1])],
                        N = l[P + 1],
                        Y = l[P + 2] - X,
                        B = l[P + 3] - N,
                        K = Math.sqrt(Y * Y + B * B);
                      t.moveTo(X + K, N),
                        t.arc(X, N, K, 0, 2 * Math.PI, !0),
                        ++R;
                      break;
                    case Po.CLOSE_PATH:
                      t.closePath(), ++R;
                      break;
                    case Po.CUSTOM:
                      (P = W[1]), (p = W[2]);
                      var Z = W[3],
                        V = W[4],
                        U = 6 == W.length ? W[5] : void 0;
                      (G.geometry = Z), (G.feature = E), R in D || (D[R] = []);
                      var H = D[R];
                      U
                        ? U(l, P, p, 2, H)
                        : ((H[0] = l[P]), (H[1] = l[P + 1]), (H.length = 2)),
                        V(H, G),
                        ++R;
                      break;
                    case Po.DRAW_IMAGE:
                      (P = W[1]),
                        (p = W[2]),
                        (m = W[3]),
                        (f = W[4]),
                        (d = W[5]);
                      var q = W[6],
                        J = W[7],
                        Q = W[8],
                        $ = W[9],
                        tt = W[10],
                        et = W[11],
                        nt = W[12],
                        it = W[13],
                        rt = W[14];
                      if (!m && W.length >= 19) {
                        (x = W[18]), (C = W[19]), (w = W[20]), (S = W[21]);
                        var ot = this.drawLabelWithPointPlacement_(x, C, w, S);
                        (m = ot.label), (W[3] = m);
                        var st = W[22];
                        (f = (ot.anchorX - st) * this.pixelRatio), (W[4] = f);
                        var at = W[23];
                        (d = (ot.anchorY - at) * this.pixelRatio),
                          (W[5] = d),
                          (q = m.height),
                          (W[6] = q),
                          (it = m.width),
                          (W[13] = it);
                      }
                      var lt = void 0;
                      W.length > 24 && (lt = W[24]);
                      var ht = void 0,
                        ut = void 0,
                        ct = void 0;
                      W.length > 16
                        ? ((ht = W[15]), (ut = W[16]), (ct = W[17]))
                        : ((ht = $r), (ut = !1), (ct = !1)),
                        tt && j ? (et += k) : tt || j || (et -= k);
                      for (var pt = 0; P < p; P += 2)
                        if (!(lt && lt[pt++] < it / this.pixelRatio)) {
                          var ft = [
                              t,
                              e,
                              m,
                              (Wt = this.calculateImageOrLabelDimensions_(
                                m.width,
                                m.height,
                                l[P],
                                l[P + 1],
                                it,
                                q,
                                f,
                                d,
                                Q,
                                $,
                                et,
                                nt,
                                r,
                                ht,
                                ut || ct,
                                E
                              )),
                              J,
                              ut ? L : null,
                              ct ? A : null,
                            ],
                            dt = void 0,
                            gt = void 0;
                          if (a && rt) {
                            var _t = p - P;
                            if (!rt[_t]) {
                              rt[_t] = ft;
                              continue;
                            }
                            if (
                              ((dt = rt[_t]),
                              delete rt[_t],
                              (gt = ss(dt)),
                              a.collides(gt))
                            )
                              continue;
                          }
                          (a && a.collides(Wt.declutterBox)) ||
                            (dt &&
                              (a && a.insert(gt),
                              this.replayImageOrLabel_.apply(this, dt)),
                            a && a.insert(Wt.declutterBox),
                            this.replayImageOrLabel_.apply(this, ft));
                        }
                      ++R;
                      break;
                    case Po.DRAW_CHARS:
                      var yt = W[1],
                        vt = W[2],
                        mt = W[3],
                        xt = W[4];
                      S = W[5];
                      var Ct = W[6],
                        wt = W[7],
                        St = W[8];
                      w = W[9];
                      var Et = W[10];
                      (x = W[11]), (C = W[12]);
                      var Tt = [W[13], W[13]],
                        bt = this.textStates[C],
                        Ot = bt.font,
                        Rt = [bt.scale[0] * wt, bt.scale[1] * wt],
                        It = void 0;
                      Ot in this.widths_
                        ? (It = this.widths_[Ot])
                        : ((It = {}), (this.widths_[Ot] = It));
                      var Pt = mi(l, yt, vt, 2),
                        Mt = Math.abs(Rt[0]) * ho(Ot, x, It);
                      if (xt || Mt <= Pt) {
                        var Ft = this.textStates[C].textAlign,
                          Lt = ts(
                            l,
                            yt,
                            vt,
                            2,
                            x,
                            (Pt - Mt) * Bo[Ft],
                            Ct,
                            Math.abs(Rt[0]),
                            ho,
                            Ot,
                            It,
                            j ? 0 : this.viewRotation_
                          );
                        t: if (Lt) {
                          var At = [],
                            Dt = void 0,
                            kt = void 0,
                            jt = void 0,
                            Gt = void 0,
                            zt = void 0;
                          if (w)
                            for (Dt = 0, kt = Lt.length; Dt < kt; ++Dt) {
                              (jt = (zt = Lt[Dt])[4]),
                                (Gt = this.createLabel(jt, C, "", w)),
                                (f = zt[2] + (Rt[0] < 0 ? -Et : Et)),
                                (d =
                                  mt * Gt.height +
                                  (2 * (0.5 - mt) * Et * Rt[1]) / Rt[0] -
                                  St);
                              var Wt = this.calculateImageOrLabelDimensions_(
                                Gt.width,
                                Gt.height,
                                zt[0],
                                zt[1],
                                Gt.width,
                                Gt.height,
                                f,
                                d,
                                0,
                                0,
                                zt[3],
                                Tt,
                                !1,
                                $r,
                                !1,
                                E
                              );
                              if (a && a.collides(Wt.declutterBox)) break t;
                              At.push([t, e, Gt, Wt, 1, null, null]);
                            }
                          if (S)
                            for (Dt = 0, kt = Lt.length; Dt < kt; ++Dt) {
                              if (
                                ((jt = (zt = Lt[Dt])[4]),
                                (Gt = this.createLabel(jt, C, S, "")),
                                (f = zt[2]),
                                (d = mt * Gt.height - St),
                                (Wt = this.calculateImageOrLabelDimensions_(
                                  Gt.width,
                                  Gt.height,
                                  zt[0],
                                  zt[1],
                                  Gt.width,
                                  Gt.height,
                                  f,
                                  d,
                                  0,
                                  0,
                                  zt[3],
                                  Tt,
                                  !1,
                                  $r,
                                  !1,
                                  E
                                )),
                                a && a.collides(Wt.declutterBox))
                              )
                                break t;
                              At.push([t, e, Gt, Wt, 1, null, null]);
                            }
                          a && a.load(At.map(ss));
                          for (var Xt = 0, Nt = At.length; Xt < Nt; ++Xt)
                            this.replayImageOrLabel_.apply(this, At[Xt]);
                        }
                      }
                      ++R;
                      break;
                    case Po.END_GEOMETRY:
                      if (void 0 !== o) {
                        var Yt = o((E = W[1]), O);
                        if (Yt) return Yt;
                      }
                      ++R;
                      break;
                    case Po.FILL:
                      z ? M++ : this.fill_(t), ++R;
                      break;
                    case Po.MOVE_TO_LINE_TO:
                      for (
                        P = W[1],
                          p = W[2],
                          T = l[P],
                          v = ((b = l[P + 1]) + 0.5) | 0,
                          ((y = (T + 0.5) | 0) === g && v === _) ||
                            (t.moveTo(T, b), (g = y), (_ = v)),
                          P += 2;
                        P < p;
                        P += 2
                      )
                        (y = ((T = l[P]) + 0.5) | 0),
                          (v = ((b = l[P + 1]) + 0.5) | 0),
                          (P != p - 2 && y === g && v === _) ||
                            (t.lineTo(T, b), (g = y), (_ = v));
                      ++R;
                      break;
                    case Po.SET_FILL_STYLE:
                      (L = W),
                        (this.alignFill_ = W[2]),
                        M &&
                          (this.fill_(t), (M = 0), F && (t.stroke(), (F = 0))),
                        (t.fillStyle = W[1]),
                        ++R;
                      break;
                    case Po.SET_STROKE_STYLE:
                      (A = W),
                        F && (t.stroke(), (F = 0)),
                        this.setStrokeStyle_(t, W),
                        ++R;
                      break;
                    case Po.STROKE:
                      z ? F++ : t.stroke(), ++R;
                      break;
                    default:
                      ++R;
                  }
                }
                M && this.fill_(t), F && t.stroke();
              }),
              (t.prototype.execute = function (t, e, n, i, r, o) {
                (this.viewRotation_ = i),
                  this.execute_(
                    t,
                    e,
                    n,
                    this.instructions,
                    r,
                    void 0,
                    void 0,
                    o
                  );
              }),
              (t.prototype.executeHitDetection = function (t, e, n, i, r) {
                return (
                  (this.viewRotation_ = n),
                  this.execute_(
                    t,
                    1,
                    e,
                    this.hitDetectionInstructions,
                    !0,
                    i,
                    r
                  )
                );
              }),
              t
            );
          })(),
          cs = us,
          ps = [Qo, Uo, Jo, qo, $o, Ho],
          fs = (function () {
            function t(t, e, n, i, r, o) {
              (this.maxExtent_ = t),
                (this.overlaps_ = i),
                (this.pixelRatio_ = n),
                (this.resolution_ = e),
                (this.renderBuffer_ = o),
                (this.executorsByZIndex_ = {}),
                (this.hitDetectionContext_ = null),
                (this.hitDetectionTransform_ = [1, 0, 0, 1, 0, 0]),
                this.createExecutors_(r);
            }
            return (
              (t.prototype.clip = function (t, e) {
                var n = this.getClipCoords(e);
                t.beginPath(),
                  t.moveTo(n[0], n[1]),
                  t.lineTo(n[2], n[3]),
                  t.lineTo(n[4], n[5]),
                  t.lineTo(n[6], n[7]),
                  t.clip();
              }),
              (t.prototype.createExecutors_ = function (t) {
                for (var e in t) {
                  var n = this.executorsByZIndex_[e];
                  void 0 === n && ((n = {}), (this.executorsByZIndex_[e] = n));
                  var i = t[e];
                  for (var r in i) {
                    var o = i[r];
                    n[r] = new cs(
                      this.resolution_,
                      this.pixelRatio_,
                      this.overlaps_,
                      o
                    );
                  }
                }
              }),
              (t.prototype.hasExecutors = function (t) {
                for (var e in this.executorsByZIndex_)
                  for (
                    var n = this.executorsByZIndex_[e], i = 0, r = t.length;
                    i < r;
                    ++i
                  )
                    if (t[i] in n) return !0;
                return !1;
              }),
              (t.prototype.forEachFeatureAtCoordinate = function (
                t,
                e,
                n,
                i,
                r,
                s
              ) {
                var a = 2 * (i = Math.round(i)) + 1,
                  l = Gn(
                    this.hitDetectionTransform_,
                    i + 0.5,
                    i + 0.5,
                    1 / e,
                    -1 / e,
                    -n,
                    -t[0],
                    -t[1]
                  ),
                  h = !this.hitDetectionContext_;
                h && (this.hitDetectionContext_ = q(a, a));
                var u,
                  c = this.hitDetectionContext_;
                c.canvas.width !== a || c.canvas.height !== a
                  ? ((c.canvas.width = a), (c.canvas.height = a))
                  : h || c.clearRect(0, 0, a, a),
                  void 0 !== this.renderBuffer_ &&
                    (we((u = [1 / 0, 1 / 0, -1 / 0, -1 / 0]), t),
                    ce(u, e * (this.renderBuffer_ + i), u));
                var p,
                  f = (function (t) {
                    if (void 0 !== ds[t]) return ds[t];
                    for (
                      var e = 2 * t + 1, n = t * t, i = new Array(n + 1), r = 0;
                      r <= t;
                      ++r
                    )
                      for (var o = 0; o <= t; ++o) {
                        var s = r * r + o * o;
                        if (s > n) break;
                        var a = i[s];
                        a || ((a = []), (i[s] = a)),
                          a.push(4 * ((t + r) * e + (t + o)) + 3),
                          r > 0 && a.push(4 * ((t - r) * e + (t + o)) + 3),
                          o > 0 &&
                            (a.push(4 * ((t + r) * e + (t - o)) + 3),
                            r > 0 && a.push(4 * ((t - r) * e + (t - o)) + 3));
                      }
                    for (var l = [], h = ((r = 0), i.length); r < h; ++r)
                      i[r] && l.push.apply(l, i[r]);
                    return (ds[t] = l), l;
                  })(i);
                function d(t, e) {
                  for (
                    var n = c.getImageData(0, 0, a, a).data,
                      o = 0,
                      l = f.length;
                    o < l;
                    o++
                  )
                    if (n[f[o]] > 0) {
                      if (!s || (p !== qo && p !== $o) || -1 !== s.indexOf(t)) {
                        var h = (f[o] - 3) / 4,
                          u = i - (h % a),
                          d = i - ((h / a) | 0),
                          g = r(t, e, u * u + d * d);
                        if (g) return g;
                      }
                      c.clearRect(0, 0, a, a);
                      break;
                    }
                }
                var g,
                  _,
                  y,
                  v,
                  m,
                  x = Object.keys(this.executorsByZIndex_).map(Number);
                for (x.sort(o), g = x.length - 1; g >= 0; --g) {
                  var C = x[g].toString();
                  for (
                    y = this.executorsByZIndex_[C], _ = ps.length - 1;
                    _ >= 0;
                    --_
                  )
                    if (
                      void 0 !== (v = y[(p = ps[_])]) &&
                      (m = v.executeHitDetection(c, l, n, d, u))
                    )
                      return m;
                }
              }),
              (t.prototype.getClipCoords = function (t) {
                var e = this.maxExtent_;
                if (!e) return null;
                var n = e[0],
                  i = e[1],
                  r = e[2],
                  o = e[3],
                  s = [n, i, n, o, r, o, r, i];
                return Xn(s, 0, 8, 2, t, s), s;
              }),
              (t.prototype.isEmpty = function () {
                return _(this.executorsByZIndex_);
              }),
              (t.prototype.execute = function (t, e, n, i, r, s, a) {
                var l = Object.keys(this.executorsByZIndex_).map(Number);
                l.sort(o), this.maxExtent_ && (t.save(), this.clip(t, n));
                var h,
                  u,
                  c,
                  p,
                  f,
                  d,
                  g = s || ps;
                for (a && l.reverse(), h = 0, u = l.length; h < u; ++h) {
                  var _ = l[h].toString();
                  for (
                    f = this.executorsByZIndex_[_], c = 0, p = g.length;
                    c < p;
                    ++c
                  )
                    void 0 !== (d = f[g[c]]) && d.execute(t, e, n, i, r, a);
                }
                this.maxExtent_ && t.restore();
              }),
              t
            );
          })(),
          ds = {},
          gs = fs,
          _s = (function () {
            var t = function (e, n) {
              return (
                (t =
                  Object.setPrototypeOf ||
                  ({ __proto__: [] } instanceof Array &&
                    function (t, e) {
                      t.__proto__ = e;
                    }) ||
                  function (t, e) {
                    for (var n in e)
                      Object.prototype.hasOwnProperty.call(e, n) &&
                        (t[n] = e[n]);
                  }),
                t(e, n)
              );
            };
            return function (e, n) {
              if ("function" != typeof n && null !== n)
                throw new TypeError(
                  "Class extends value " +
                    String(n) +
                    " is not a constructor or null"
                );
              function i() {
                this.constructor = e;
              }
              t(e, n),
                (e.prototype =
                  null === n
                    ? Object.create(n)
                    : ((i.prototype = n.prototype), new i()));
            };
          })(),
          ys = (function (t) {
            function e(e, n, i, r, o, s, a) {
              var l = t.call(this) || this;
              return (
                (l.context_ = e),
                (l.pixelRatio_ = n),
                (l.extent_ = i),
                (l.transform_ = r),
                (l.viewRotation_ = o),
                (l.squaredTolerance_ = s),
                (l.userTransform_ = a),
                (l.contextFillState_ = null),
                (l.contextStrokeState_ = null),
                (l.contextTextState_ = null),
                (l.fillState_ = null),
                (l.strokeState_ = null),
                (l.image_ = null),
                (l.imageAnchorX_ = 0),
                (l.imageAnchorY_ = 0),
                (l.imageHeight_ = 0),
                (l.imageOpacity_ = 0),
                (l.imageOriginX_ = 0),
                (l.imageOriginY_ = 0),
                (l.imageRotateWithView_ = !1),
                (l.imageRotation_ = 0),
                (l.imageScale_ = [0, 0]),
                (l.imageWidth_ = 0),
                (l.text_ = ""),
                (l.textOffsetX_ = 0),
                (l.textOffsetY_ = 0),
                (l.textRotateWithView_ = !1),
                (l.textRotation_ = 0),
                (l.textScale_ = [0, 0]),
                (l.textFillState_ = null),
                (l.textStrokeState_ = null),
                (l.textState_ = null),
                (l.pixelCoordinates_ = []),
                (l.tmpLocalTransform_ = [1, 0, 0, 1, 0, 0]),
                l
              );
            }
            return (
              _s(e, t),
              (e.prototype.drawImages_ = function (t, e, n, i) {
                if (this.image_) {
                  var r = Xn(
                      t,
                      e,
                      n,
                      i,
                      this.transform_,
                      this.pixelCoordinates_
                    ),
                    o = this.context_,
                    s = this.tmpLocalTransform_,
                    a = o.globalAlpha;
                  1 != this.imageOpacity_ &&
                    (o.globalAlpha = a * this.imageOpacity_);
                  var l = this.imageRotation_;
                  this.imageRotateWithView_ && (l += this.viewRotation_);
                  for (var h = 0, u = r.length; h < u; h += 2) {
                    var c = r[h] - this.imageAnchorX_,
                      p = r[h + 1] - this.imageAnchorY_;
                    if (
                      0 !== l ||
                      1 != this.imageScale_[0] ||
                      1 != this.imageScale_[1]
                    ) {
                      var f = c + this.imageAnchorX_,
                        d = p + this.imageAnchorY_;
                      Gn(s, f, d, 1, 1, l, -f, -d),
                        o.setTransform.apply(o, s),
                        o.translate(f, d),
                        o.scale(this.imageScale_[0], this.imageScale_[1]),
                        o.drawImage(
                          this.image_,
                          this.imageOriginX_,
                          this.imageOriginY_,
                          this.imageWidth_,
                          this.imageHeight_,
                          -this.imageAnchorX_,
                          -this.imageAnchorY_,
                          this.imageWidth_,
                          this.imageHeight_
                        ),
                        o.setTransform(1, 0, 0, 1, 0, 0);
                    } else
                      o.drawImage(
                        this.image_,
                        this.imageOriginX_,
                        this.imageOriginY_,
                        this.imageWidth_,
                        this.imageHeight_,
                        c,
                        p,
                        this.imageWidth_,
                        this.imageHeight_
                      );
                  }
                  1 != this.imageOpacity_ && (o.globalAlpha = a);
                }
              }),
              (e.prototype.drawText_ = function (t, e, n, i) {
                if (this.textState_ && "" !== this.text_) {
                  this.textFillState_ &&
                    this.setContextFillState_(this.textFillState_),
                    this.textStrokeState_ &&
                      this.setContextStrokeState_(this.textStrokeState_),
                    this.setContextTextState_(this.textState_);
                  var r = Xn(
                      t,
                      e,
                      n,
                      i,
                      this.transform_,
                      this.pixelCoordinates_
                    ),
                    o = this.context_,
                    s = this.textRotation_;
                  for (
                    this.textRotateWithView_ && (s += this.viewRotation_);
                    e < n;
                    e += i
                  ) {
                    var a = r[e] + this.textOffsetX_,
                      l = r[e + 1] + this.textOffsetY_;
                    if (
                      0 !== s ||
                      1 != this.textScale_[0] ||
                      1 != this.textScale_[1]
                    ) {
                      var h = Gn(
                        this.tmpLocalTransform_,
                        a,
                        l,
                        1,
                        1,
                        s,
                        -a,
                        -l
                      );
                      o.setTransform.apply(o, h),
                        o.translate(a, l),
                        o.scale(this.textScale_[0], this.textScale_[1]),
                        this.textStrokeState_ && o.strokeText(this.text_, 0, 0),
                        this.textFillState_ && o.fillText(this.text_, 0, 0),
                        o.setTransform(1, 0, 0, 1, 0, 0);
                    } else
                      this.textStrokeState_ && o.strokeText(this.text_, a, l),
                        this.textFillState_ && o.fillText(this.text_, a, l);
                  }
                }
              }),
              (e.prototype.moveToLineTo_ = function (t, e, n, i, r) {
                var o = this.context_,
                  s = Xn(t, e, n, i, this.transform_, this.pixelCoordinates_);
                o.moveTo(s[0], s[1]);
                var a = s.length;
                r && (a -= 2);
                for (var l = 2; l < a; l += 2) o.lineTo(s[l], s[l + 1]);
                return r && o.closePath(), n;
              }),
              (e.prototype.drawRings_ = function (t, e, n, i) {
                for (var r = 0, o = n.length; r < o; ++r)
                  e = this.moveToLineTo_(t, e, n[r], i, !0);
                return e;
              }),
              (e.prototype.drawCircle = function (t) {
                if (je(this.extent_, t.getExtent())) {
                  if (this.fillState_ || this.strokeState_) {
                    this.fillState_ &&
                      this.setContextFillState_(this.fillState_),
                      this.strokeState_ &&
                        this.setContextStrokeState_(this.strokeState_);
                    var e = (function (t, e, n) {
                        var i = t.getFlatCoordinates();
                        if (i) {
                          var r = t.getStride();
                          return Xn(i, 0, i.length, r, e, n);
                        }
                        return null;
                      })(t, this.transform_, this.pixelCoordinates_),
                      n = e[2] - e[0],
                      i = e[3] - e[1],
                      r = Math.sqrt(n * n + i * i),
                      o = this.context_;
                    o.beginPath(),
                      o.arc(e[0], e[1], r, 0, 2 * Math.PI),
                      this.fillState_ && o.fill(),
                      this.strokeState_ && o.stroke();
                  }
                  "" !== this.text_ && this.drawText_(t.getCenter(), 0, 2, 2);
                }
              }),
              (e.prototype.setStyle = function (t) {
                this.setFillStrokeStyle(t.getFill(), t.getStroke()),
                  this.setImageStyle(t.getImage()),
                  this.setTextStyle(t.getText());
              }),
              (e.prototype.setTransform = function (t) {
                this.transform_ = t;
              }),
              (e.prototype.drawGeometry = function (t) {
                switch (t.getType()) {
                  case In:
                    this.drawPoint(t);
                    break;
                  case Pn:
                    this.drawLineString(t);
                    break;
                  case Mn:
                    this.drawPolygon(t);
                    break;
                  case Fn:
                    this.drawMultiPoint(t);
                    break;
                  case Ln:
                    this.drawMultiLineString(t);
                    break;
                  case An:
                    this.drawMultiPolygon(t);
                    break;
                  case Dn:
                    this.drawGeometryCollection(t);
                    break;
                  case kn:
                    this.drawCircle(t);
                }
              }),
              (e.prototype.drawFeature = function (t, e) {
                var n = e.getGeometryFunction()(t);
                n &&
                  je(this.extent_, n.getExtent()) &&
                  (this.setStyle(e), this.drawGeometry(n));
              }),
              (e.prototype.drawGeometryCollection = function (t) {
                for (
                  var e = t.getGeometriesArray(), n = 0, i = e.length;
                  n < i;
                  ++n
                )
                  this.drawGeometry(e[n]);
              }),
              (e.prototype.drawPoint = function (t) {
                this.squaredTolerance_ &&
                  (t = t.simplifyTransformed(
                    this.squaredTolerance_,
                    this.userTransform_
                  ));
                var e = t.getFlatCoordinates(),
                  n = t.getStride();
                this.image_ && this.drawImages_(e, 0, e.length, n),
                  "" !== this.text_ && this.drawText_(e, 0, e.length, n);
              }),
              (e.prototype.drawMultiPoint = function (t) {
                this.squaredTolerance_ &&
                  (t = t.simplifyTransformed(
                    this.squaredTolerance_,
                    this.userTransform_
                  ));
                var e = t.getFlatCoordinates(),
                  n = t.getStride();
                this.image_ && this.drawImages_(e, 0, e.length, n),
                  "" !== this.text_ && this.drawText_(e, 0, e.length, n);
              }),
              (e.prototype.drawLineString = function (t) {
                if (
                  (this.squaredTolerance_ &&
                    (t = t.simplifyTransformed(
                      this.squaredTolerance_,
                      this.userTransform_
                    )),
                  je(this.extent_, t.getExtent()))
                ) {
                  if (this.strokeState_) {
                    this.setContextStrokeState_(this.strokeState_);
                    var e = this.context_,
                      n = t.getFlatCoordinates();
                    e.beginPath(),
                      this.moveToLineTo_(n, 0, n.length, t.getStride(), !1),
                      e.stroke();
                  }
                  if ("" !== this.text_) {
                    var i = t.getFlatMidpoint();
                    this.drawText_(i, 0, 2, 2);
                  }
                }
              }),
              (e.prototype.drawMultiLineString = function (t) {
                this.squaredTolerance_ &&
                  (t = t.simplifyTransformed(
                    this.squaredTolerance_,
                    this.userTransform_
                  ));
                var e = t.getExtent();
                if (je(this.extent_, e)) {
                  if (this.strokeState_) {
                    this.setContextStrokeState_(this.strokeState_);
                    var n = this.context_,
                      i = t.getFlatCoordinates(),
                      r = 0,
                      o = t.getEnds(),
                      s = t.getStride();
                    n.beginPath();
                    for (var a = 0, l = o.length; a < l; ++a)
                      r = this.moveToLineTo_(i, r, o[a], s, !1);
                    n.stroke();
                  }
                  if ("" !== this.text_) {
                    var h = t.getFlatMidpoints();
                    this.drawText_(h, 0, h.length, 2);
                  }
                }
              }),
              (e.prototype.drawPolygon = function (t) {
                if (
                  (this.squaredTolerance_ &&
                    (t = t.simplifyTransformed(
                      this.squaredTolerance_,
                      this.userTransform_
                    )),
                  je(this.extent_, t.getExtent()))
                ) {
                  if (this.strokeState_ || this.fillState_) {
                    this.fillState_ &&
                      this.setContextFillState_(this.fillState_),
                      this.strokeState_ &&
                        this.setContextStrokeState_(this.strokeState_);
                    var e = this.context_;
                    e.beginPath(),
                      this.drawRings_(
                        t.getOrientedFlatCoordinates(),
                        0,
                        t.getEnds(),
                        t.getStride()
                      ),
                      this.fillState_ && e.fill(),
                      this.strokeState_ && e.stroke();
                  }
                  if ("" !== this.text_) {
                    var n = t.getFlatInteriorPoint();
                    this.drawText_(n, 0, 2, 2);
                  }
                }
              }),
              (e.prototype.drawMultiPolygon = function (t) {
                if (
                  (this.squaredTolerance_ &&
                    (t = t.simplifyTransformed(
                      this.squaredTolerance_,
                      this.userTransform_
                    )),
                  je(this.extent_, t.getExtent()))
                ) {
                  if (this.strokeState_ || this.fillState_) {
                    this.fillState_ &&
                      this.setContextFillState_(this.fillState_),
                      this.strokeState_ &&
                        this.setContextStrokeState_(this.strokeState_);
                    var e = this.context_,
                      n = t.getOrientedFlatCoordinates(),
                      i = 0,
                      r = t.getEndss(),
                      o = t.getStride();
                    e.beginPath();
                    for (var s = 0, a = r.length; s < a; ++s) {
                      var l = r[s];
                      i = this.drawRings_(n, i, l, o);
                    }
                    this.fillState_ && e.fill(),
                      this.strokeState_ && e.stroke();
                  }
                  if ("" !== this.text_) {
                    var h = t.getFlatInteriorPoints();
                    this.drawText_(h, 0, h.length, 2);
                  }
                }
              }),
              (e.prototype.setContextFillState_ = function (t) {
                var e = this.context_,
                  n = this.contextFillState_;
                n
                  ? n.fillStyle != t.fillStyle &&
                    ((n.fillStyle = t.fillStyle), (e.fillStyle = t.fillStyle))
                  : ((e.fillStyle = t.fillStyle),
                    (this.contextFillState_ = { fillStyle: t.fillStyle }));
              }),
              (e.prototype.setContextStrokeState_ = function (t) {
                var e = this.context_,
                  n = this.contextStrokeState_;
                n
                  ? (n.lineCap != t.lineCap &&
                      ((n.lineCap = t.lineCap), (e.lineCap = t.lineCap)),
                    e.setLineDash &&
                      (h(n.lineDash, t.lineDash) ||
                        e.setLineDash((n.lineDash = t.lineDash)),
                      n.lineDashOffset != t.lineDashOffset &&
                        ((n.lineDashOffset = t.lineDashOffset),
                        (e.lineDashOffset = t.lineDashOffset))),
                    n.lineJoin != t.lineJoin &&
                      ((n.lineJoin = t.lineJoin), (e.lineJoin = t.lineJoin)),
                    n.lineWidth != t.lineWidth &&
                      ((n.lineWidth = t.lineWidth),
                      (e.lineWidth = t.lineWidth)),
                    n.miterLimit != t.miterLimit &&
                      ((n.miterLimit = t.miterLimit),
                      (e.miterLimit = t.miterLimit)),
                    n.strokeStyle != t.strokeStyle &&
                      ((n.strokeStyle = t.strokeStyle),
                      (e.strokeStyle = t.strokeStyle)))
                  : ((e.lineCap = t.lineCap),
                    e.setLineDash &&
                      (e.setLineDash(t.lineDash),
                      (e.lineDashOffset = t.lineDashOffset)),
                    (e.lineJoin = t.lineJoin),
                    (e.lineWidth = t.lineWidth),
                    (e.miterLimit = t.miterLimit),
                    (e.strokeStyle = t.strokeStyle),
                    (this.contextStrokeState_ = {
                      lineCap: t.lineCap,
                      lineDash: t.lineDash,
                      lineDashOffset: t.lineDashOffset,
                      lineJoin: t.lineJoin,
                      lineWidth: t.lineWidth,
                      miterLimit: t.miterLimit,
                      strokeStyle: t.strokeStyle,
                    }));
              }),
              (e.prototype.setContextTextState_ = function (t) {
                var e = this.context_,
                  n = this.contextTextState_,
                  i = t.textAlign ? t.textAlign : Jr;
                n
                  ? (n.font != t.font && ((n.font = t.font), (e.font = t.font)),
                    n.textAlign != i && ((n.textAlign = i), (e.textAlign = i)),
                    n.textBaseline != t.textBaseline &&
                      ((n.textBaseline = t.textBaseline),
                      (e.textBaseline = t.textBaseline)))
                  : ((e.font = t.font),
                    (e.textAlign = i),
                    (e.textBaseline = t.textBaseline),
                    (this.contextTextState_ = {
                      font: t.font,
                      textAlign: i,
                      textBaseline: t.textBaseline,
                    }));
              }),
              (e.prototype.setFillStrokeStyle = function (t, e) {
                var n = this;
                if (t) {
                  var i = t.getColor();
                  this.fillState_ = { fillStyle: Br(i || Zr) };
                } else this.fillState_ = null;
                if (e) {
                  var r = e.getColor(),
                    o = e.getLineCap(),
                    s = e.getLineDash(),
                    a = e.getLineDashOffset(),
                    l = e.getLineJoin(),
                    h = e.getWidth(),
                    u = e.getMiterLimit(),
                    c = s || Ur;
                  this.strokeState_ = {
                    lineCap: void 0 !== o ? o : Vr,
                    lineDash:
                      1 === this.pixelRatio_
                        ? c
                        : c.map(function (t) {
                            return t * n.pixelRatio_;
                          }),
                    lineDashOffset: (a || 0) * this.pixelRatio_,
                    lineJoin: void 0 !== l ? l : Hr,
                    lineWidth: (void 0 !== h ? h : 1) * this.pixelRatio_,
                    miterLimit: void 0 !== u ? u : 10,
                    strokeStyle: Br(r || qr),
                  };
                } else this.strokeState_ = null;
              }),
              (e.prototype.setImageStyle = function (t) {
                var e;
                if (t && (e = t.getSize())) {
                  var n = t.getAnchor(),
                    i = t.getOrigin();
                  (this.image_ = t.getImage(this.pixelRatio_)),
                    (this.imageAnchorX_ = n[0] * this.pixelRatio_),
                    (this.imageAnchorY_ = n[1] * this.pixelRatio_),
                    (this.imageHeight_ = e[1] * this.pixelRatio_),
                    (this.imageOpacity_ = t.getOpacity()),
                    (this.imageOriginX_ = i[0]),
                    (this.imageOriginY_ = i[1]),
                    (this.imageRotateWithView_ = t.getRotateWithView()),
                    (this.imageRotation_ = t.getRotation()),
                    (this.imageScale_ = t.getScaleArray()),
                    (this.imageWidth_ = e[0] * this.pixelRatio_);
                } else this.image_ = null;
              }),
              (e.prototype.setTextStyle = function (t) {
                if (t) {
                  var e = t.getFill();
                  if (e) {
                    var n = e.getColor();
                    this.textFillState_ = { fillStyle: Br(n || Zr) };
                  } else this.textFillState_ = null;
                  var i = t.getStroke();
                  if (i) {
                    var r = i.getColor(),
                      o = i.getLineCap(),
                      s = i.getLineDash(),
                      a = i.getLineDashOffset(),
                      l = i.getLineJoin(),
                      h = i.getWidth(),
                      u = i.getMiterLimit();
                    this.textStrokeState_ = {
                      lineCap: void 0 !== o ? o : Vr,
                      lineDash: s || Ur,
                      lineDashOffset: a || 0,
                      lineJoin: void 0 !== l ? l : Hr,
                      lineWidth: void 0 !== h ? h : 1,
                      miterLimit: void 0 !== u ? u : 10,
                      strokeStyle: Br(r || qr),
                    };
                  } else this.textStrokeState_ = null;
                  var c = t.getFont(),
                    p = t.getOffsetX(),
                    f = t.getOffsetY(),
                    d = t.getRotateWithView(),
                    g = t.getRotation(),
                    _ = t.getScaleArray(),
                    y = t.getText(),
                    v = t.getTextAlign(),
                    m = t.getTextBaseline();
                  (this.textState_ = {
                    font: void 0 !== c ? c : Kr,
                    textAlign: void 0 !== v ? v : Jr,
                    textBaseline: void 0 !== m ? m : Qr,
                  }),
                    (this.text_ =
                      void 0 !== y
                        ? Array.isArray(y)
                          ? y.reduce(function (t, e, n) {
                              return t + (n % 2 ? " " : e);
                            }, "")
                          : y
                        : ""),
                    (this.textOffsetX_ =
                      void 0 !== p ? this.pixelRatio_ * p : 0),
                    (this.textOffsetY_ =
                      void 0 !== f ? this.pixelRatio_ * f : 0),
                    (this.textRotateWithView_ = void 0 !== d && d),
                    (this.textRotation_ = void 0 !== g ? g : 0),
                    (this.textScale_ = [
                      this.pixelRatio_ * _[0],
                      this.pixelRatio_ * _[1],
                    ]);
                } else this.text_ = "";
              }),
              e
            );
          })(Mo),
          vs = ys,
          ms = "fraction",
          xs = "pixels",
          Cs = "bottom-left",
          ws = "bottom-right",
          Ss = "top-left",
          Es = "top-right";
        function Ts(t, e, n) {
          return e + ":" + t + ":" + (n ? sr(n) : "null");
        }
        var bs = new ((function () {
            function t() {
              (this.cache_ = {}),
                (this.cacheSize_ = 0),
                (this.maxCacheSize_ = 32);
            }
            return (
              (t.prototype.clear = function () {
                (this.cache_ = {}), (this.cacheSize_ = 0);
              }),
              (t.prototype.canExpireCache = function () {
                return this.cacheSize_ > this.maxCacheSize_;
              }),
              (t.prototype.expire = function () {
                if (this.canExpireCache()) {
                  var t = 0;
                  for (var e in this.cache_) {
                    var n = this.cache_[e];
                    0 != (3 & t++) ||
                      n.hasListener() ||
                      (delete this.cache_[e], --this.cacheSize_);
                  }
                }
              }),
              (t.prototype.get = function (t, e, n) {
                var i = Ts(t, e, n);
                return i in this.cache_ ? this.cache_[i] : null;
              }),
              (t.prototype.set = function (t, e, n, i) {
                var r = Ts(t, e, n);
                (this.cache_[r] = i), ++this.cacheSize_;
              }),
              (t.prototype.setSize = function (t) {
                (this.maxCacheSize_ = t), this.expire();
              }),
              t
            );
          })())(),
          Os = (function () {
            var t = function (e, n) {
              return (
                (t =
                  Object.setPrototypeOf ||
                  ({ __proto__: [] } instanceof Array &&
                    function (t, e) {
                      t.__proto__ = e;
                    }) ||
                  function (t, e) {
                    for (var n in e)
                      Object.prototype.hasOwnProperty.call(e, n) &&
                        (t[n] = e[n]);
                  }),
                t(e, n)
              );
            };
            return function (e, n) {
              if ("function" != typeof n && null !== n)
                throw new TypeError(
                  "Class extends value " +
                    String(n) +
                    " is not a constructor or null"
                );
              function i() {
                this.constructor = e;
              }
              t(e, n),
                (e.prototype =
                  null === n
                    ? Object.create(n)
                    : ((i.prototype = n.prototype), new i()));
            };
          })(),
          Rs = null,
          Is = (function (t) {
            function e(e, n, i, r, o, s) {
              var a = t.call(this) || this;
              return (
                (a.hitDetectionImage_ = null),
                (a.image_ = e || new Image()),
                null !== r && (a.image_.crossOrigin = r),
                (a.canvas_ = {}),
                (a.color_ = s),
                (a.unlisten_ = null),
                (a.imageState_ = o),
                (a.size_ = i),
                (a.src_ = n),
                a.tainted_,
                a
              );
            }
            return (
              Os(e, t),
              (e.prototype.isTainted_ = function () {
                if (void 0 === this.tainted_ && 2 === this.imageState_) {
                  Rs || (Rs = q(1, 1)), Rs.drawImage(this.image_, 0, 0);
                  try {
                    Rs.getImageData(0, 0, 1, 1), (this.tainted_ = !1);
                  } catch (t) {
                    (Rs = null), (this.tainted_ = !0);
                  }
                }
                return !0 === this.tainted_;
              }),
              (e.prototype.dispatchChangeEvent_ = function () {
                this.dispatchEvent(x);
              }),
              (e.prototype.handleImageError_ = function () {
                (this.imageState_ = 3),
                  this.unlistenImage_(),
                  this.dispatchChangeEvent_();
              }),
              (e.prototype.handleImageLoad_ = function () {
                (this.imageState_ = 2),
                  this.size_
                    ? ((this.image_.width = this.size_[0]),
                      (this.image_.height = this.size_[1]))
                    : (this.size_ = [this.image_.width, this.image_.height]),
                  this.unlistenImage_(),
                  this.dispatchChangeEvent_();
              }),
              (e.prototype.getImage = function (t) {
                return (
                  this.replaceColor_(t),
                  this.canvas_[t] ? this.canvas_[t] : this.image_
                );
              }),
              (e.prototype.getPixelRatio = function (t) {
                return this.replaceColor_(t), this.canvas_[t] ? t : 1;
              }),
              (e.prototype.getImageState = function () {
                return this.imageState_;
              }),
              (e.prototype.getHitDetectionImage = function () {
                if (!this.hitDetectionImage_)
                  if (this.isTainted_()) {
                    var t = this.size_[0],
                      e = this.size_[1],
                      n = q(t, e);
                    n.fillRect(0, 0, t, e),
                      (this.hitDetectionImage_ = n.canvas);
                  } else this.hitDetectionImage_ = this.image_;
                return this.hitDetectionImage_;
              }),
              (e.prototype.getSize = function () {
                return this.size_;
              }),
              (e.prototype.getSrc = function () {
                return this.src_;
              }),
              (e.prototype.load = function () {
                if (0 == this.imageState_) {
                  this.imageState_ = 1;
                  try {
                    this.image_.src = this.src_;
                  } catch (t) {
                    this.handleImageError_();
                  }
                  this.unlisten_ = xr(
                    this.image_,
                    this.handleImageLoad_.bind(this),
                    this.handleImageError_.bind(this)
                  );
                }
              }),
              (e.prototype.replaceColor_ = function (t) {
                if (this.color_ && !this.canvas_[t] && 2 === this.imageState_) {
                  var e = document.createElement("canvas");
                  (this.canvas_[t] = e),
                    (e.width = Math.ceil(this.image_.width * t)),
                    (e.height = Math.ceil(this.image_.height * t));
                  var n = e.getContext("2d");
                  if (
                    (n.scale(t, t),
                    n.drawImage(this.image_, 0, 0),
                    (n.globalCompositeOperation = "multiply"),
                    "multiply" === n.globalCompositeOperation ||
                      this.isTainted_())
                  )
                    (n.fillStyle = sr(this.color_)),
                      n.fillRect(0, 0, e.width / t, e.height / t),
                      (n.globalCompositeOperation = "destination-in"),
                      n.drawImage(this.image_, 0, 0);
                  else {
                    for (
                      var i = n.getImageData(0, 0, e.width, e.height),
                        r = i.data,
                        o = this.color_[0] / 255,
                        s = this.color_[1] / 255,
                        a = this.color_[2] / 255,
                        l = this.color_[3],
                        h = 0,
                        u = r.length;
                      h < u;
                      h += 4
                    )
                      (r[h] *= o),
                        (r[h + 1] *= s),
                        (r[h + 2] *= a),
                        (r[h + 3] *= l);
                    n.putImageData(i, 0, 0);
                  }
                }
              }),
              (e.prototype.unlistenImage_ = function () {
                this.unlisten_ && (this.unlisten_(), (this.unlisten_ = null));
              }),
              e
            );
          })(m),
          Ps = (function () {
            var t = function (e, n) {
              return (
                (t =
                  Object.setPrototypeOf ||
                  ({ __proto__: [] } instanceof Array &&
                    function (t, e) {
                      t.__proto__ = e;
                    }) ||
                  function (t, e) {
                    for (var n in e)
                      Object.prototype.hasOwnProperty.call(e, n) &&
                        (t[n] = e[n]);
                  }),
                t(e, n)
              );
            };
            return function (e, n) {
              if ("function" != typeof n && null !== n)
                throw new TypeError(
                  "Class extends value " +
                    String(n) +
                    " is not a constructor or null"
                );
              function i() {
                this.constructor = e;
              }
              t(e, n),
                (e.prototype =
                  null === n
                    ? Object.create(n)
                    : ((i.prototype = n.prototype), new i()));
            };
          })(),
          Ms = (function (t) {
            function e(e) {
              var n = this,
                i = e || {},
                r = void 0 !== i.opacity ? i.opacity : 1,
                o = void 0 !== i.rotation ? i.rotation : 0,
                s = void 0 !== i.scale ? i.scale : 1,
                a = void 0 !== i.rotateWithView && i.rotateWithView;
              ((n =
                t.call(this, {
                  opacity: r,
                  rotation: o,
                  scale: s,
                  displacement:
                    void 0 !== i.displacement ? i.displacement : [0, 0],
                  rotateWithView: a,
                }) || this).anchor_ =
                void 0 !== i.anchor ? i.anchor : [0.5, 0.5]),
                (n.normalizedAnchor_ = null),
                (n.anchorOrigin_ =
                  void 0 !== i.anchorOrigin ? i.anchorOrigin : Ss),
                (n.anchorXUnits_ =
                  void 0 !== i.anchorXUnits ? i.anchorXUnits : ms),
                (n.anchorYUnits_ =
                  void 0 !== i.anchorYUnits ? i.anchorYUnits : ms),
                (n.crossOrigin_ =
                  void 0 !== i.crossOrigin ? i.crossOrigin : null);
              var l = void 0 !== i.img ? i.img : null;
              n.imgSize_ = i.imgSize;
              var h = i.src;
              vt(!(void 0 !== h && l), 4),
                vt(!l || (l && n.imgSize_), 5),
                (void 0 !== h && 0 !== h.length) || !l || (h = l.src || D(l)),
                vt(void 0 !== h && h.length > 0, 6);
              var u = void 0 !== i.src ? 0 : 2;
              return (
                (n.color_ = void 0 !== i.color ? lr(i.color) : null),
                (n.iconImage_ = (function (t, e, n, i, r, o) {
                  var s = bs.get(e, i, o);
                  return (
                    s || ((s = new Is(t, e, n, i, r, o)), bs.set(e, i, o, s)), s
                  );
                })(
                  l,
                  h,
                  void 0 !== n.imgSize_ ? n.imgSize_ : null,
                  n.crossOrigin_,
                  u,
                  n.color_
                )),
                (n.offset_ = void 0 !== i.offset ? i.offset : [0, 0]),
                (n.offsetOrigin_ =
                  void 0 !== i.offsetOrigin ? i.offsetOrigin : Ss),
                (n.origin_ = null),
                (n.size_ = void 0 !== i.size ? i.size : null),
                n
              );
            }
            return (
              Ps(e, t),
              (e.prototype.clone = function () {
                var t = this.getScale();
                return new e({
                  anchor: this.anchor_.slice(),
                  anchorOrigin: this.anchorOrigin_,
                  anchorXUnits: this.anchorXUnits_,
                  anchorYUnits: this.anchorYUnits_,
                  color:
                    this.color_ && this.color_.slice
                      ? this.color_.slice()
                      : this.color_ || void 0,
                  crossOrigin: this.crossOrigin_,
                  imgSize: this.imgSize_,
                  offset: this.offset_.slice(),
                  offsetOrigin: this.offsetOrigin_,
                  opacity: this.getOpacity(),
                  rotateWithView: this.getRotateWithView(),
                  rotation: this.getRotation(),
                  scale: Array.isArray(t) ? t.slice() : t,
                  size: null !== this.size_ ? this.size_.slice() : void 0,
                  src: this.getSrc(),
                });
              }),
              (e.prototype.getAnchor = function () {
                var t = this.normalizedAnchor_;
                if (!t) {
                  t = this.anchor_;
                  var e = this.getSize();
                  if (this.anchorXUnits_ == ms || this.anchorYUnits_ == ms) {
                    if (!e) return null;
                    (t = this.anchor_.slice()),
                      this.anchorXUnits_ == ms && (t[0] *= e[0]),
                      this.anchorYUnits_ == ms && (t[1] *= e[1]);
                  }
                  if (this.anchorOrigin_ != Ss) {
                    if (!e) return null;
                    t === this.anchor_ && (t = this.anchor_.slice()),
                      (this.anchorOrigin_ != Es && this.anchorOrigin_ != ws) ||
                        (t[0] = -t[0] + e[0]),
                      (this.anchorOrigin_ != Cs && this.anchorOrigin_ != ws) ||
                        (t[1] = -t[1] + e[1]);
                  }
                  this.normalizedAnchor_ = t;
                }
                var n = this.getDisplacement();
                return [t[0] - n[0], t[1] + n[1]];
              }),
              (e.prototype.setAnchor = function (t) {
                (this.anchor_ = t), (this.normalizedAnchor_ = null);
              }),
              (e.prototype.getColor = function () {
                return this.color_;
              }),
              (e.prototype.getImage = function (t) {
                return this.iconImage_.getImage(t);
              }),
              (e.prototype.getPixelRatio = function (t) {
                return this.iconImage_.getPixelRatio(t);
              }),
              (e.prototype.getImageSize = function () {
                return this.iconImage_.getSize();
              }),
              (e.prototype.getImageState = function () {
                return this.iconImage_.getImageState();
              }),
              (e.prototype.getHitDetectionImage = function () {
                return this.iconImage_.getHitDetectionImage();
              }),
              (e.prototype.getOrigin = function () {
                if (this.origin_) return this.origin_;
                var t = this.offset_;
                if (this.offsetOrigin_ != Ss) {
                  var e = this.getSize(),
                    n = this.iconImage_.getSize();
                  if (!e || !n) return null;
                  (t = t.slice()),
                    (this.offsetOrigin_ != Es && this.offsetOrigin_ != ws) ||
                      (t[0] = n[0] - e[0] - t[0]),
                    (this.offsetOrigin_ != Cs && this.offsetOrigin_ != ws) ||
                      (t[1] = n[1] - e[1] - t[1]);
                }
                return (this.origin_ = t), this.origin_;
              }),
              (e.prototype.getSrc = function () {
                return this.iconImage_.getSrc();
              }),
              (e.prototype.getSize = function () {
                return this.size_ ? this.size_ : this.iconImage_.getSize();
              }),
              (e.prototype.listenImageChange = function (t) {
                this.iconImage_.addEventListener(x, t);
              }),
              (e.prototype.load = function () {
                this.iconImage_.load();
              }),
              (e.prototype.unlistenImageChange = function (t) {
                this.iconImage_.removeEventListener(x, t);
              }),
              e
            );
          })(Yr),
          Fs = 0.5,
          Ls = {
            Point: function (t, e, n, i, r) {
              var o,
                s = n.getImage(),
                a = n.getText();
              if (
                (r && ((t = r), (o = s && a && a.getText() ? {} : void 0)), s)
              ) {
                if (2 != s.getImageState()) return;
                var l = t.getBuilder(n.getZIndex(), qo);
                l.setImageStyle(s, o), l.drawPoint(e, i);
              }
              if (a && a.getText()) {
                var h = t.getBuilder(n.getZIndex(), $o);
                h.setTextStyle(a, o), h.drawText(e, i);
              }
            },
            LineString: function (t, e, n, i, r) {
              var o = n.getStroke();
              if (o) {
                var s = t.getBuilder(n.getZIndex(), Jo);
                s.setFillStrokeStyle(null, o), s.drawLineString(e, i);
              }
              var a = n.getText();
              if (a && a.getText()) {
                var l = (r || t).getBuilder(n.getZIndex(), $o);
                l.setTextStyle(a), l.drawText(e, i);
              }
            },
            Polygon: function (t, e, n, i, r) {
              var o = n.getFill(),
                s = n.getStroke();
              if (o || s) {
                var a = t.getBuilder(n.getZIndex(), Qo);
                a.setFillStrokeStyle(o, s), a.drawPolygon(e, i);
              }
              var l = n.getText();
              if (l && l.getText()) {
                var h = (r || t).getBuilder(n.getZIndex(), $o);
                h.setTextStyle(l), h.drawText(e, i);
              }
            },
            MultiPoint: function (t, e, n, i, r) {
              var o,
                s = n.getImage(),
                a = n.getText();
              if (
                (r && ((t = r), (o = s && a && a.getText() ? {} : void 0)), s)
              ) {
                if (2 != s.getImageState()) return;
                var l = t.getBuilder(n.getZIndex(), qo);
                l.setImageStyle(s, o), l.drawMultiPoint(e, i);
              }
              if (a && a.getText()) {
                var h = (r || t).getBuilder(n.getZIndex(), $o);
                h.setTextStyle(a, o), h.drawText(e, i);
              }
            },
            MultiLineString: function (t, e, n, i, r) {
              var o = n.getStroke();
              if (o) {
                var s = t.getBuilder(n.getZIndex(), Jo);
                s.setFillStrokeStyle(null, o), s.drawMultiLineString(e, i);
              }
              var a = n.getText();
              if (a && a.getText()) {
                var l = (r || t).getBuilder(n.getZIndex(), $o);
                l.setTextStyle(a), l.drawText(e, i);
              }
            },
            MultiPolygon: function (t, e, n, i, r) {
              var o = n.getFill(),
                s = n.getStroke();
              if (s || o) {
                var a = t.getBuilder(n.getZIndex(), Qo);
                a.setFillStrokeStyle(o, s), a.drawMultiPolygon(e, i);
              }
              var l = n.getText();
              if (l && l.getText()) {
                var h = (r || t).getBuilder(n.getZIndex(), $o);
                h.setTextStyle(l), h.drawText(e, i);
              }
            },
            GeometryCollection: function (t, e, n, i, r) {
              var o,
                s,
                a = e.getGeometriesArray();
              for (o = 0, s = a.length; o < s; ++o)
                (0, Ls[a[o].getType()])(t, a[o], n, i, r);
            },
            Circle: function (t, e, n, i, r) {
              var o = n.getFill(),
                s = n.getStroke();
              if (o || s) {
                var a = t.getBuilder(n.getZIndex(), Uo);
                a.setFillStrokeStyle(o, s), a.drawCircle(e, i);
              }
              var l = n.getText();
              if (l && l.getText()) {
                var h = (r || t).getBuilder(n.getZIndex(), $o);
                h.setTextStyle(l), h.drawText(e, i);
              }
            },
          };
        function As(t, e) {
          return parseInt(D(t), 10) - parseInt(D(e), 10);
        }
        function Ds(t, e) {
          return (0.5 * t) / e;
        }
        function ks(t, e, n, i, r, o, s) {
          var a = !1,
            l = n.getImage();
          if (l) {
            var h = l.getImageState();
            2 == h || 3 == h
              ? l.unlistenImageChange(r)
              : (0 == h && l.load(),
                (h = l.getImageState()),
                l.listenImageChange(r),
                (a = !0));
          }
          return (
            (function (t, e, n, i, r, o) {
              var s = n.getGeometryFunction()(e);
              if (s) {
                var a = s.simplifyTransformed(i, r);
                n.getRenderer()
                  ? js(t, a, n, e)
                  : (0, Ls[a.getType()])(t, a, n, e, o);
              }
            })(t, e, n, i, o, s),
            a
          );
        }
        function js(t, e, n, i) {
          if (e.getType() != Dn)
            t.getBuilder(n.getZIndex(), Ho).drawCustom(
              e,
              i,
              n.getRenderer(),
              n.getHitDetectionRenderer()
            );
          else
            for (var r = e.getGeometries(), o = 0, s = r.length; o < s; ++o)
              js(t, r[o], n, i);
        }
        var Gs = (function () {
            var t = function (e, n) {
              return (
                (t =
                  Object.setPrototypeOf ||
                  ({ __proto__: [] } instanceof Array &&
                    function (t, e) {
                      t.__proto__ = e;
                    }) ||
                  function (t, e) {
                    for (var n in e)
                      Object.prototype.hasOwnProperty.call(e, n) &&
                        (t[n] = e[n]);
                  }),
                t(e, n)
              );
            };
            return function (e, n) {
              if ("function" != typeof n && null !== n)
                throw new TypeError(
                  "Class extends value " +
                    String(n) +
                    " is not a constructor or null"
                );
              function i() {
                this.constructor = e;
              }
              t(e, n),
                (e.prototype =
                  null === n
                    ? Object.create(n)
                    : ((i.prototype = n.prototype), new i()));
            };
          })(),
          zs = (function (t) {
            function e(e) {
              var n = t.call(this, e) || this;
              return (
                (n.boundHandleStyleImageChange_ =
                  n.handleStyleImageChange_.bind(n)),
                n.animatingOrInteracting_,
                (n.dirty_ = !1),
                (n.hitDetectionImageData_ = null),
                (n.renderedFeatures_ = null),
                (n.renderedRevision_ = -1),
                (n.renderedResolution_ = NaN),
                (n.renderedExtent_ = [1 / 0, 1 / 0, -1 / 0, -1 / 0]),
                (n.wrappedRenderedExtent_ = [1 / 0, 1 / 0, -1 / 0, -1 / 0]),
                n.renderedRotation_,
                (n.renderedCenter_ = null),
                (n.renderedProjection_ = null),
                (n.renderedRenderOrder_ = null),
                (n.replayGroup_ = null),
                (n.replayGroupChanged = !0),
                (n.declutterExecutorGroup = null),
                (n.clipping = !0),
                n
              );
            }
            return (
              Gs(e, t),
              (e.prototype.renderWorlds = function (t, e, n) {
                var i = e.extent,
                  r = e.viewState,
                  o = r.center,
                  s = r.resolution,
                  a = r.projection,
                  l = r.rotation,
                  h = a.getExtent(),
                  u = this.getLayer().getSource(),
                  c = e.pixelRatio,
                  p = e.viewHints,
                  f = !(p[0] || p[1]),
                  d = this.context,
                  g = Math.round(e.size[0] * c),
                  _ = Math.round(e.size[1] * c),
                  y = u.getWrapX() && a.canWrapX(),
                  v = y ? ke(h) : null,
                  m = y ? Math.ceil((i[2] - h[2]) / v) + 1 : 1,
                  x = y ? Math.floor((i[0] - h[0]) / v) : 0;
                do {
                  var C = this.getRenderTransform(o, s, l, c, g, _, x * v);
                  t.execute(d, 1, C, l, f, void 0, n);
                } while (++x < m);
              }),
              (e.prototype.renderDeclutter = function (t) {
                this.declutterExecutorGroup &&
                  this.renderWorlds(
                    this.declutterExecutorGroup,
                    t,
                    t.declutterTree
                  );
              }),
              (e.prototype.renderFrame = function (t, e) {
                var n = t.pixelRatio,
                  i = t.layerStatesArray[t.layerIndex];
                !(function (t, e, n) {
                  !(function (t, e, n, i, r, o, s) {
                    (t[0] = e),
                      (t[1] = n),
                      (t[2] = i),
                      (t[3] = r),
                      (t[4] = o),
                      (t[5] = s);
                  })(t, e, 0, 0, n, 0, 0);
                })(this.pixelTransform, 1 / n, 1 / n),
                  zn(this.inversePixelTransform, this.pixelTransform);
                var r = Wn(this.pixelTransform);
                this.useContainer(e, r, i.opacity, this.getBackground(t));
                var o = this.context,
                  s = o.canvas,
                  a = this.replayGroup_,
                  l = this.declutterExecutorGroup;
                if ((!a || a.isEmpty()) && (!l || l.isEmpty())) return null;
                var h = Math.round(t.size[0] * n),
                  u = Math.round(t.size[1] * n);
                s.width != h || s.height != u
                  ? ((s.width = h),
                    (s.height = u),
                    s.style.transform !== r && (s.style.transform = r))
                  : this.containerReused || o.clearRect(0, 0, h, u),
                  this.preRender(o, t);
                var c = t.viewState,
                  p = (c.projection, !1),
                  f = !0;
                if (i.extent && this.clipping) {
                  var d = pn(i.extent);
                  (p = (f = je(d, t.extent)) && !ge(d, t.extent)) &&
                    this.clipUnrotated(o, t, d);
                }
                f && this.renderWorlds(a, t),
                  p && o.restore(),
                  this.postRender(o, t);
                var g = st(i.opacity),
                  _ = this.container;
                return (
                  g !== _.style.opacity && (_.style.opacity = g),
                  this.renderedRotation_ !== c.rotation &&
                    ((this.renderedRotation_ = c.rotation),
                    (this.hitDetectionImageData_ = null)),
                  this.container
                );
              }),
              (e.prototype.getFeatures = function (t) {
                return new Promise(
                  function (e) {
                    if (
                      !this.hitDetectionImageData_ &&
                      !this.animatingOrInteracting_
                    ) {
                      var n = [
                        this.context.canvas.width,
                        this.context.canvas.height,
                      ];
                      jn(this.pixelTransform, n);
                      var i = this.renderedCenter_,
                        r = this.renderedResolution_,
                        s = this.renderedRotation_,
                        a = this.renderedProjection_,
                        l = this.wrappedRenderedExtent_,
                        h = this.getLayer(),
                        u = [],
                        c = n[0] * Fs,
                        p = n[1] * Fs;
                      u.push(
                        this.getRenderTransform(i, r, s, Fs, c, p, 0).slice()
                      );
                      var f = h.getSource(),
                        d = a.getExtent();
                      if (f.getWrapX() && a.canWrapX() && !ge(d, l)) {
                        for (
                          var g = l[0], _ = ke(d), y = 0, v = void 0;
                          g < d[0];

                        )
                          (v = _ * --y),
                            u.push(
                              this.getRenderTransform(
                                i,
                                r,
                                s,
                                Fs,
                                c,
                                p,
                                v
                              ).slice()
                            ),
                            (g += _);
                        for (y = 0, g = l[2]; g > d[2]; )
                          (v = _ * ++y),
                            u.push(
                              this.getRenderTransform(
                                i,
                                r,
                                s,
                                Fs,
                                c,
                                p,
                                v
                              ).slice()
                            ),
                            (g -= _);
                      }
                      this.hitDetectionImageData_ = (function (
                        t,
                        e,
                        n,
                        i,
                        r,
                        s,
                        a
                      ) {
                        var l = q(t[0] * Fs, t[1] * Fs);
                        l.imageSmoothingEnabled = !1;
                        for (
                          var h = l.canvas,
                            u = new vs(l, Fs, r, null, a),
                            c = n.length,
                            p = Math.floor(16777215 / c),
                            f = {},
                            d = 1;
                          d <= c;
                          ++d
                        ) {
                          var g = n[d - 1],
                            _ = g.getStyleFunction() || i;
                          if (i) {
                            var y = _(g, s);
                            if (y) {
                              Array.isArray(y) || (y = [y]);
                              for (
                                var v =
                                    "#" +
                                    ("000000" + (d * p).toString(16)).slice(-6),
                                  m = 0,
                                  x = y.length;
                                m < x;
                                ++m
                              ) {
                                var C = y[m],
                                  w = C.getGeometryFunction()(g);
                                if (w && je(r, w.getExtent())) {
                                  var S = C.clone(),
                                    E = S.getFill();
                                  E && E.setColor(v);
                                  var T = S.getStroke();
                                  T && (T.setColor(v), T.setLineDash(null)),
                                    S.setText(void 0);
                                  var b = C.getImage();
                                  if (b && 0 !== b.getOpacity()) {
                                    var O = b.getImageSize();
                                    if (!O) continue;
                                    var R = q(O[0], O[1], void 0, {
                                        alpha: !1,
                                      }),
                                      I = R.canvas;
                                    (R.fillStyle = v),
                                      R.fillRect(0, 0, I.width, I.height),
                                      S.setImage(
                                        new Ms({
                                          img: I,
                                          imgSize: O,
                                          anchor: b.getAnchor(),
                                          anchorXUnits: xs,
                                          anchorYUnits: xs,
                                          offset: b.getOrigin(),
                                          opacity: 1,
                                          size: b.getSize(),
                                          scale: b.getScale(),
                                          rotation: b.getRotation(),
                                          rotateWithView: b.getRotateWithView(),
                                        })
                                      );
                                  }
                                  var P = S.getZIndex() || 0;
                                  (L = f[P]) ||
                                    ((L = {}),
                                    (f[P] = L),
                                    (L.Polygon = []),
                                    (L.Circle = []),
                                    (L.LineString = []),
                                    (L.Point = [])),
                                    L[w.getType().replace("Multi", "")].push(
                                      w,
                                      S
                                    );
                                }
                              }
                            }
                          }
                        }
                        for (
                          var M = Object.keys(f).map(Number).sort(o),
                            F = ((d = 0), M.length);
                          d < F;
                          ++d
                        ) {
                          var L = f[M[d]];
                          for (var A in L) {
                            var D = L[A];
                            for (m = 0, x = D.length; m < x; m += 2) {
                              u.setStyle(D[m + 1]);
                              for (var k = 0, j = e.length; k < j; ++k)
                                u.setTransform(e[k]), u.drawGeometry(D[m]);
                            }
                          }
                        }
                        return l.getImageData(0, 0, h.width, h.height);
                      })(
                        n,
                        u,
                        this.renderedFeatures_,
                        h.getStyleFunction(),
                        l,
                        r,
                        s
                      );
                    }
                    e(
                      (function (t, e, n) {
                        var i = [];
                        if (n) {
                          var r = Math.floor(Math.round(t[0]) * Fs),
                            o = Math.floor(Math.round(t[1]) * Fs),
                            s =
                              4 *
                              (mt(r, 0, n.width - 1) +
                                mt(o, 0, n.height - 1) * n.width),
                            a = n.data[s],
                            l = n.data[s + 1],
                            h = n.data[s + 2] + 256 * (l + 256 * a),
                            u = Math.floor(16777215 / e.length);
                          h && h % u == 0 && i.push(e[h / u - 1]);
                        }
                        return i;
                      })(t, this.renderedFeatures_, this.hitDetectionImageData_)
                    );
                  }.bind(this)
                );
              }),
              (e.prototype.forEachFeatureAtCoordinate = function (
                t,
                e,
                n,
                i,
                r
              ) {
                var o = this;
                if (this.replayGroup_) {
                  var s,
                    a = e.viewState.resolution,
                    l = e.viewState.rotation,
                    h = this.getLayer(),
                    u = {},
                    c = function (t, e, n) {
                      var o = D(t),
                        s = u[o];
                      if (s) {
                        if (!0 !== s && n < s.distanceSq) {
                          if (0 === n)
                            return (
                              (u[o] = !0),
                              r.splice(r.lastIndexOf(s), 1),
                              i(t, h, e)
                            );
                          (s.geometry = e), (s.distanceSq = n);
                        }
                      } else {
                        if (0 === n) return (u[o] = !0), i(t, h, e);
                        r.push(
                          (u[o] = {
                            feature: t,
                            layer: h,
                            geometry: e,
                            distanceSq: n,
                            callback: i,
                          })
                        );
                      }
                    },
                    p = [this.replayGroup_];
                  return (
                    this.declutterExecutorGroup &&
                      p.push(this.declutterExecutorGroup),
                    p.some(function (i) {
                      return (s = i.forEachFeatureAtCoordinate(
                        t,
                        a,
                        l,
                        n,
                        c,
                        i === o.declutterExecutorGroup && e.declutterTree
                          ? e.declutterTree.all().map(function (t) {
                              return t.value;
                            })
                          : null
                      ));
                    }),
                    s
                  );
                }
              }),
              (e.prototype.handleFontsChanged = function () {
                var t = this.getLayer();
                t.getVisible() && this.replayGroup_ && t.changed();
              }),
              (e.prototype.handleStyleImageChange_ = function (t) {
                this.renderIfReadyAndVisible();
              }),
              (e.prototype.prepareFrame = function (t) {
                var e = this.getLayer(),
                  n = e.getSource();
                if (!n) return !1;
                var i = t.viewHints[0],
                  r = t.viewHints[1],
                  o = e.getUpdateWhileAnimating(),
                  s = e.getUpdateWhileInteracting();
                if ((!this.dirty_ && !o && i) || (!s && r))
                  return (this.animatingOrInteracting_ = !0), !0;
                this.animatingOrInteracting_ = !1;
                var a = t.extent,
                  l = t.viewState,
                  u = l.projection,
                  c = l.resolution,
                  p = t.pixelRatio,
                  f = e.getRevision(),
                  d = e.getRenderBuffer(),
                  g = e.getRenderOrder();
                void 0 === g && (g = As);
                var _ = l.center.slice(),
                  y = ce(a, d * c),
                  v = y.slice(),
                  m = [y.slice()],
                  x = u.getExtent();
                if (n.getWrapX() && u.canWrapX() && !ge(x, t.extent)) {
                  var C = ke(x),
                    w = Math.max(ke(y) / 2, C);
                  (y[0] = x[0] - w), (y[2] = x[2] + w), Xe(_, u);
                  var S = (function (t, e) {
                    var n = e.getExtent(),
                      i = Ie(t);
                    if (e.canWrapX() && (i[0] < n[0] || i[0] >= n[2])) {
                      var r = ke(n),
                        o = Math.floor((i[0] - n[0]) / r) * r;
                      (t[0] -= o), (t[2] -= o);
                    }
                    return t;
                  })(m[0], u);
                  S[0] < x[0] && S[2] < x[2]
                    ? m.push([S[0] + C, S[1], S[2] + C, S[3]])
                    : S[0] > x[0] &&
                      S[2] > x[2] &&
                      m.push([S[0] - C, S[1], S[2] - C, S[3]]);
                }
                if (
                  !this.dirty_ &&
                  this.renderedResolution_ == c &&
                  this.renderedRevision_ == f &&
                  this.renderedRenderOrder_ == g &&
                  ge(this.wrappedRenderedExtent_, y)
                )
                  return (
                    h(this.renderedExtent_, v) ||
                      ((this.hitDetectionImageData_ = null),
                      (this.renderedExtent_ = v)),
                    (this.renderedCenter_ = _),
                    (this.replayGroupChanged = !1),
                    !0
                  );
                (this.replayGroup_ = null), (this.dirty_ = !1);
                var E,
                  T = new Vo(Ds(c, p), y, c, p);
                this.getLayer().getDeclutter() &&
                  (E = new Vo(Ds(c, p), y, c, p));
                var b,
                  O = ln();
                if (O) {
                  for (var R = 0, I = m.length; R < I; ++R) {
                    var P = cn(m[R]);
                    n.loadFeatures(P, fn(c), O);
                  }
                  b = $e(O, u);
                } else
                  for (R = 0, I = m.length; R < I; ++R)
                    n.loadFeatures(m[R], c, u);
                var M = (function (t, e) {
                    var n = Ds(t, e);
                    return n * n;
                  })(c, p),
                  F = function (t) {
                    var n,
                      i = t.getStyleFunction() || e.getStyleFunction();
                    if ((i && (n = i(t, c)), n)) {
                      var r = this.renderFeature(t, M, n, T, b, E);
                      this.dirty_ = this.dirty_ || r;
                    }
                  }.bind(this),
                  L = cn(y),
                  A = n.getFeaturesInExtent(L);
                for (g && A.sort(g), R = 0, I = A.length; R < I; ++R) F(A[R]);
                this.renderedFeatures_ = A;
                var D = T.finish(),
                  k = new gs(y, c, p, n.getOverlaps(), D, e.getRenderBuffer());
                return (
                  E &&
                    (this.declutterExecutorGroup = new gs(
                      y,
                      c,
                      p,
                      n.getOverlaps(),
                      E.finish(),
                      e.getRenderBuffer()
                    )),
                  (this.renderedResolution_ = c),
                  (this.renderedRevision_ = f),
                  (this.renderedRenderOrder_ = g),
                  (this.renderedExtent_ = v),
                  (this.wrappedRenderedExtent_ = y),
                  (this.renderedCenter_ = _),
                  (this.renderedProjection_ = u),
                  (this.replayGroup_ = k),
                  (this.hitDetectionImageData_ = null),
                  (this.replayGroupChanged = !0),
                  !0
                );
              }),
              (e.prototype.renderFeature = function (t, e, n, i, r, o) {
                if (!n) return !1;
                var s = !1;
                if (Array.isArray(n))
                  for (var a = 0, l = n.length; a < l; ++a)
                    s =
                      ks(
                        i,
                        t,
                        n[a],
                        e,
                        this.boundHandleStyleImageChange_,
                        r,
                        o
                      ) || s;
                else
                  s = ks(i, t, n, e, this.boundHandleStyleImageChange_, r, o);
                return s;
              }),
              e
            );
          })(dr),
          Ws = zs,
          Xs = (function () {
            var t = function (e, n) {
              return (
                (t =
                  Object.setPrototypeOf ||
                  ({ __proto__: [] } instanceof Array &&
                    function (t, e) {
                      t.__proto__ = e;
                    }) ||
                  function (t, e) {
                    for (var n in e)
                      Object.prototype.hasOwnProperty.call(e, n) &&
                        (t[n] = e[n]);
                  }),
                t(e, n)
              );
            };
            return function (e, n) {
              if ("function" != typeof n && null !== n)
                throw new TypeError(
                  "Class extends value " +
                    String(n) +
                    " is not a constructor or null"
                );
              function i() {
                this.constructor = e;
              }
              t(e, n),
                (e.prototype =
                  null === n
                    ? Object.create(n)
                    : ((i.prototype = n.prototype), new i()));
            };
          })(),
          Ns = (function (t) {
            function e(e) {
              return t.call(this, e) || this;
            }
            return (
              Xs(e, t),
              (e.prototype.createRenderer = function () {
                return new Ws(this);
              }),
              e
            );
          })(Eo),
          Ys = (function () {
            function t(t) {
              (this.highWaterMark = void 0 !== t ? t : 2048),
                (this.count_ = 0),
                (this.entries_ = {}),
                (this.oldest_ = null),
                (this.newest_ = null);
            }
            return (
              (t.prototype.canExpireCache = function () {
                return (
                  this.highWaterMark > 0 && this.getCount() > this.highWaterMark
                );
              }),
              (t.prototype.expireCache = function (t) {
                for (; this.canExpireCache(); ) this.pop();
              }),
              (t.prototype.clear = function () {
                (this.count_ = 0),
                  (this.entries_ = {}),
                  (this.oldest_ = null),
                  (this.newest_ = null);
              }),
              (t.prototype.containsKey = function (t) {
                return this.entries_.hasOwnProperty(t);
              }),
              (t.prototype.forEach = function (t) {
                for (var e = this.oldest_; e; )
                  t(e.value_, e.key_, this), (e = e.newer);
              }),
              (t.prototype.get = function (t, e) {
                var n = this.entries_[t];
                return (
                  vt(void 0 !== n, 15),
                  n === this.newest_ ||
                    (n === this.oldest_
                      ? ((this.oldest_ = this.oldest_.newer),
                        (this.oldest_.older = null))
                      : ((n.newer.older = n.older), (n.older.newer = n.newer)),
                    (n.newer = null),
                    (n.older = this.newest_),
                    (this.newest_.newer = n),
                    (this.newest_ = n)),
                  n.value_
                );
              }),
              (t.prototype.remove = function (t) {
                var e = this.entries_[t];
                return (
                  vt(void 0 !== e, 15),
                  e === this.newest_
                    ? ((this.newest_ = e.older),
                      this.newest_ && (this.newest_.newer = null))
                    : e === this.oldest_
                    ? ((this.oldest_ = e.newer),
                      this.oldest_ && (this.oldest_.older = null))
                    : ((e.newer.older = e.older), (e.older.newer = e.newer)),
                  delete this.entries_[t],
                  --this.count_,
                  e.value_
                );
              }),
              (t.prototype.getCount = function () {
                return this.count_;
              }),
              (t.prototype.getKeys = function () {
                var t,
                  e = new Array(this.count_),
                  n = 0;
                for (t = this.newest_; t; t = t.older) e[n++] = t.key_;
                return e;
              }),
              (t.prototype.getValues = function () {
                var t,
                  e = new Array(this.count_),
                  n = 0;
                for (t = this.newest_; t; t = t.older) e[n++] = t.value_;
                return e;
              }),
              (t.prototype.peekLast = function () {
                return this.oldest_.value_;
              }),
              (t.prototype.peekLastKey = function () {
                return this.oldest_.key_;
              }),
              (t.prototype.peekFirstKey = function () {
                return this.newest_.key_;
              }),
              (t.prototype.pop = function () {
                var t = this.oldest_;
                return (
                  delete this.entries_[t.key_],
                  t.newer && (t.newer.older = null),
                  (this.oldest_ = t.newer),
                  this.oldest_ || (this.newest_ = null),
                  --this.count_,
                  t.value_
                );
              }),
              (t.prototype.replace = function (t, e) {
                this.get(t), (this.entries_[t].value_ = e);
              }),
              (t.prototype.set = function (t, e) {
                vt(!(t in this.entries_), 16);
                var n = {
                  key_: t,
                  newer: null,
                  older: this.newest_,
                  value_: e,
                };
                this.newest_ ? (this.newest_.newer = n) : (this.oldest_ = n),
                  (this.newest_ = n),
                  (this.entries_[t] = n),
                  ++this.count_;
              }),
              (t.prototype.setSize = function (t) {
                this.highWaterMark = t;
              }),
              t
            );
          })();
        function Bs(t, e, n, i) {
          return void 0 !== i
            ? ((i[0] = t), (i[1] = e), (i[2] = n), i)
            : [t, e, n];
        }
        function Ks(t, e, n) {
          return t + "/" + e + "/" + n;
        }
        function Zs(t) {
          return Ks(t[0], t[1], t[2]);
        }
        var Vs = (function () {
            var t = function (e, n) {
              return (
                (t =
                  Object.setPrototypeOf ||
                  ({ __proto__: [] } instanceof Array &&
                    function (t, e) {
                      t.__proto__ = e;
                    }) ||
                  function (t, e) {
                    for (var n in e)
                      Object.prototype.hasOwnProperty.call(e, n) &&
                        (t[n] = e[n]);
                  }),
                t(e, n)
              );
            };
            return function (e, n) {
              if ("function" != typeof n && null !== n)
                throw new TypeError(
                  "Class extends value " +
                    String(n) +
                    " is not a constructor or null"
                );
              function i() {
                this.constructor = e;
              }
              t(e, n),
                (e.prototype =
                  null === n
                    ? Object.create(n)
                    : ((i.prototype = n.prototype), new i()));
            };
          })(),
          Us = (function (t) {
            function e() {
              return (null !== t && t.apply(this, arguments)) || this;
            }
            return (
              Vs(e, t),
              (e.prototype.expireCache = function (t) {
                for (
                  ;
                  this.canExpireCache() && !(this.peekLast().getKey() in t);

                )
                  this.pop().release();
              }),
              (e.prototype.pruneExceptNewestZ = function () {
                if (0 !== this.getCount()) {
                  var t = this.peekFirstKey().split("/").map(Number)[0];
                  this.forEach(
                    function (e) {
                      e.tileCoord[0] !== t &&
                        (this.remove(Zs(e.tileCoord)), e.release());
                    }.bind(this)
                  );
                }
              }),
              e
            );
          })(Ys),
          Hs = Us,
          qs = (function () {
            var t = function (e, n) {
              return (
                (t =
                  Object.setPrototypeOf ||
                  ({ __proto__: [] } instanceof Array &&
                    function (t, e) {
                      t.__proto__ = e;
                    }) ||
                  function (t, e) {
                    for (var n in e)
                      Object.prototype.hasOwnProperty.call(e, n) &&
                        (t[n] = e[n]);
                  }),
                t(e, n)
              );
            };
            return function (e, n) {
              if ("function" != typeof n && null !== n)
                throw new TypeError(
                  "Class extends value " +
                    String(n) +
                    " is not a constructor or null"
                );
              function i() {
                this.constructor = e;
              }
              t(e, n),
                (e.prototype =
                  null === n
                    ? Object.create(n)
                    : ((i.prototype = n.prototype), new i()));
            };
          })();
        function Js(t) {
          return t
            ? Array.isArray(t)
              ? function (e) {
                  return t;
                }
              : "function" == typeof t
              ? t
              : function (e) {
                  return [t];
                }
            : null;
        }
        var Qs = (function (t) {
            function e(e) {
              var n = t.call(this) || this;
              (n.projection = Ue(e.projection)),
                (n.attributions_ = Js(e.attributions)),
                (n.attributionsCollapsible_ =
                  void 0 === e.attributionsCollapsible ||
                  e.attributionsCollapsible),
                (n.loading = !1),
                (n.state_ = void 0 !== e.state ? e.state : Dt),
                (n.wrapX_ = void 0 !== e.wrapX && e.wrapX),
                (n.interpolate_ = !!e.interpolate),
                (n.viewResolver = null),
                (n.viewRejector = null);
              var i = n;
              return (
                (n.viewPromise_ = new Promise(function (t, e) {
                  (i.viewResolver = t), (i.viewRejector = e);
                })),
                n
              );
            }
            return (
              qs(e, t),
              (e.prototype.getAttributions = function () {
                return this.attributions_;
              }),
              (e.prototype.getAttributionsCollapsible = function () {
                return this.attributionsCollapsible_;
              }),
              (e.prototype.getProjection = function () {
                return this.projection;
              }),
              (e.prototype.getResolutions = function () {
                return L();
              }),
              (e.prototype.getView = function () {
                return this.viewPromise_;
              }),
              (e.prototype.getState = function () {
                return this.state_;
              }),
              (e.prototype.getWrapX = function () {
                return this.wrapX_;
              }),
              (e.prototype.getInterpolate = function () {
                return this.interpolate_;
              }),
              (e.prototype.refresh = function () {
                this.changed();
              }),
              (e.prototype.setAttributions = function (t) {
                (this.attributions_ = Js(t)), this.changed();
              }),
              (e.prototype.setState = function (t) {
                (this.state_ = t), this.changed();
              }),
              e
            );
          })(G),
          $s = [0, 0, 0],
          ta = (function () {
            function t(t) {
              var e, n, i;
              if (
                ((this.minZoom = void 0 !== t.minZoom ? t.minZoom : 0),
                (this.resolutions_ = t.resolutions),
                vt(
                  ((e = this.resolutions_),
                  !0,
                  (n =
                    function (t, e) {
                      return e - t;
                    } || o),
                  e.every(function (t, i) {
                    if (0 === i) return !0;
                    var r = n(e[i - 1], t);
                    return !(r > 0 || 0 === r);
                  })),
                  17
                ),
                !t.origins)
              )
                for (var r = 0, s = this.resolutions_.length - 1; r < s; ++r)
                  if (i) {
                    if (this.resolutions_[r] / this.resolutions_[r + 1] !== i) {
                      i = void 0;
                      break;
                    }
                  } else i = this.resolutions_[r] / this.resolutions_[r + 1];
              (this.zoomFactor_ = i),
                (this.maxZoom = this.resolutions_.length - 1),
                (this.origin_ = void 0 !== t.origin ? t.origin : null),
                (this.origins_ = null),
                void 0 !== t.origins &&
                  ((this.origins_ = t.origins),
                  vt(this.origins_.length == this.resolutions_.length, 20));
              var a = t.extent;
              void 0 === a ||
                this.origin_ ||
                this.origins_ ||
                (this.origin_ = Ae(a)),
                vt(
                  (!this.origin_ && this.origins_) ||
                    (this.origin_ && !this.origins_),
                  18
                ),
                (this.tileSizes_ = null),
                void 0 !== t.tileSizes &&
                  ((this.tileSizes_ = t.tileSizes),
                  vt(this.tileSizes_.length == this.resolutions_.length, 19)),
                (this.tileSize_ =
                  void 0 !== t.tileSize
                    ? t.tileSize
                    : this.tileSizes_
                    ? null
                    : 256),
                vt(
                  (!this.tileSize_ && this.tileSizes_) ||
                    (this.tileSize_ && !this.tileSizes_),
                  22
                ),
                (this.extent_ = void 0 !== a ? a : null),
                (this.fullTileRanges_ = null),
                (this.tmpSize_ = [0, 0]),
                (this.tmpExtent_ = [0, 0, 0, 0]),
                void 0 !== t.sizes
                  ? (this.fullTileRanges_ = t.sizes.map(function (t, e) {
                      var n = new Ar(
                        Math.min(0, t[0]),
                        Math.max(t[0] - 1, -1),
                        Math.min(0, t[1]),
                        Math.max(t[1] - 1, -1)
                      );
                      if (a) {
                        var i = this.getTileRangeForExtentAndZ(a, e);
                        (n.minX = Math.max(i.minX, n.minX)),
                          (n.maxX = Math.min(i.maxX, n.maxX)),
                          (n.minY = Math.max(i.minY, n.minY)),
                          (n.maxY = Math.min(i.maxY, n.maxY));
                      }
                      return n;
                    }, this))
                  : a && this.calculateTileRanges_(a);
            }
            return (
              (t.prototype.forEachTileCoord = function (t, e, n) {
                for (
                  var i = this.getTileRangeForExtentAndZ(t, e),
                    r = i.minX,
                    o = i.maxX;
                  r <= o;
                  ++r
                )
                  for (var s = i.minY, a = i.maxY; s <= a; ++s) n([e, r, s]);
              }),
              (t.prototype.forEachTileCoordParentTileRange = function (
                t,
                e,
                n,
                i
              ) {
                var r,
                  o,
                  s = null,
                  a = t[0] - 1;
                for (
                  2 === this.zoomFactor_
                    ? ((r = t[1]), (o = t[2]))
                    : (s = this.getTileCoordExtent(t, i));
                  a >= this.minZoom;

                ) {
                  if (
                    e(
                      a,
                      2 === this.zoomFactor_
                        ? Lr(
                            (r = Math.floor(r / 2)),
                            r,
                            (o = Math.floor(o / 2)),
                            o,
                            n
                          )
                        : this.getTileRangeForExtentAndZ(s, a, n)
                    )
                  )
                    return !0;
                  --a;
                }
                return !1;
              }),
              (t.prototype.getExtent = function () {
                return this.extent_;
              }),
              (t.prototype.getMaxZoom = function () {
                return this.maxZoom;
              }),
              (t.prototype.getMinZoom = function () {
                return this.minZoom;
              }),
              (t.prototype.getOrigin = function (t) {
                return this.origin_ ? this.origin_ : this.origins_[t];
              }),
              (t.prototype.getResolution = function (t) {
                return this.resolutions_[t];
              }),
              (t.prototype.getResolutions = function () {
                return this.resolutions_;
              }),
              (t.prototype.getTileCoordChildTileRange = function (t, e, n) {
                if (t[0] < this.maxZoom) {
                  if (2 === this.zoomFactor_) {
                    var i = 2 * t[1],
                      r = 2 * t[2];
                    return Lr(i, i + 1, r, r + 1, e);
                  }
                  var o = this.getTileCoordExtent(t, n || this.tmpExtent_);
                  return this.getTileRangeForExtentAndZ(o, t[0] + 1, e);
                }
                return null;
              }),
              (t.prototype.getTileRangeForTileCoordAndZ = function (t, e, n) {
                if (e > this.maxZoom || e < this.minZoom) return null;
                var i = t[0],
                  r = t[1],
                  o = t[2];
                if (e === i) return Lr(r, o, r, o, n);
                if (this.zoomFactor_) {
                  var s = Math.pow(this.zoomFactor_, e - i),
                    a = Math.floor(r * s),
                    l = Math.floor(o * s);
                  return e < i
                    ? Lr(a, a, l, l, n)
                    : Lr(
                        a,
                        Math.floor(s * (r + 1)) - 1,
                        l,
                        Math.floor(s * (o + 1)) - 1,
                        n
                      );
                }
                var h = this.getTileCoordExtent(t, this.tmpExtent_);
                return this.getTileRangeForExtentAndZ(h, e, n);
              }),
              (t.prototype.getTileRangeExtent = function (t, e, n) {
                var i = this.getOrigin(t),
                  r = this.getResolution(t),
                  o = kr(this.getTileSize(t), this.tmpSize_),
                  s = i[0] + e.minX * o[0] * r,
                  a = i[0] + (e.maxX + 1) * o[0] * r;
                return ve(
                  s,
                  i[1] + e.minY * o[1] * r,
                  a,
                  i[1] + (e.maxY + 1) * o[1] * r,
                  n
                );
              }),
              (t.prototype.getTileRangeForExtentAndZ = function (t, e, n) {
                var i = $s;
                this.getTileCoordForXYAndZ_(t[0], t[3], e, !1, i);
                var r = i[1],
                  o = i[2];
                return (
                  this.getTileCoordForXYAndZ_(t[2], t[1], e, !0, i),
                  Lr(r, i[1], o, i[2], n)
                );
              }),
              (t.prototype.getTileCoordCenter = function (t) {
                var e = this.getOrigin(t[0]),
                  n = this.getResolution(t[0]),
                  i = kr(this.getTileSize(t[0]), this.tmpSize_);
                return [
                  e[0] + (t[1] + 0.5) * i[0] * n,
                  e[1] - (t[2] + 0.5) * i[1] * n,
                ];
              }),
              (t.prototype.getTileCoordExtent = function (t, e) {
                var n = this.getOrigin(t[0]),
                  i = this.getResolution(t[0]),
                  r = kr(this.getTileSize(t[0]), this.tmpSize_),
                  o = n[0] + t[1] * r[0] * i,
                  s = n[1] - (t[2] + 1) * r[1] * i;
                return ve(o, s, o + r[0] * i, s + r[1] * i, e);
              }),
              (t.prototype.getTileCoordForCoordAndResolution = function (
                t,
                e,
                n
              ) {
                return this.getTileCoordForXYAndResolution_(
                  t[0],
                  t[1],
                  e,
                  !1,
                  n
                );
              }),
              (t.prototype.getTileCoordForXYAndResolution_ = function (
                t,
                e,
                n,
                i,
                r
              ) {
                var o = this.getZForResolution(n),
                  s = n / this.getResolution(o),
                  a = this.getOrigin(o),
                  l = kr(this.getTileSize(o), this.tmpSize_),
                  h = (s * (t - a[0])) / n / l[0],
                  u = (s * (a[1] - e)) / n / l[1];
                return (
                  i
                    ? ((h = It(h, 5) - 1), (u = It(u, 5) - 1))
                    : ((h = Rt(h, 5)), (u = Rt(u, 5))),
                  Bs(o, h, u, r)
                );
              }),
              (t.prototype.getTileCoordForXYAndZ_ = function (t, e, n, i, r) {
                var o = this.getOrigin(n),
                  s = this.getResolution(n),
                  a = kr(this.getTileSize(n), this.tmpSize_),
                  l = (t - o[0]) / s / a[0],
                  h = (o[1] - e) / s / a[1];
                return (
                  i
                    ? ((l = It(l, 5) - 1), (h = It(h, 5) - 1))
                    : ((l = Rt(l, 5)), (h = Rt(h, 5))),
                  Bs(n, l, h, r)
                );
              }),
              (t.prototype.getTileCoordForCoordAndZ = function (t, e, n) {
                return this.getTileCoordForXYAndZ_(t[0], t[1], e, !1, n);
              }),
              (t.prototype.getTileCoordResolution = function (t) {
                return this.resolutions_[t[0]];
              }),
              (t.prototype.getTileSize = function (t) {
                return this.tileSize_ ? this.tileSize_ : this.tileSizes_[t];
              }),
              (t.prototype.getFullTileRange = function (t) {
                return this.fullTileRanges_
                  ? this.fullTileRanges_[t]
                  : this.extent_
                  ? this.getTileRangeForExtentAndZ(this.extent_, t)
                  : null;
              }),
              (t.prototype.getZForResolution = function (t, e) {
                return mt(
                  s(this.resolutions_, t, e || 0),
                  this.minZoom,
                  this.maxZoom
                );
              }),
              (t.prototype.calculateTileRanges_ = function (t) {
                for (
                  var e = this.resolutions_.length,
                    n = new Array(e),
                    i = this.minZoom;
                  i < e;
                  ++i
                )
                  n[i] = this.getTileRangeForExtentAndZ(t, i);
                this.fullTileRanges_ = n;
              }),
              t
            );
          })();
        function ea(t) {
          var e = t.getDefaultTileGrid();
          return (
            e ||
              ((e = (function (t, e, n, i) {
                return (function (t, e, n, i) {
                  var r = na(t, undefined, n);
                  return new ta({
                    extent: t,
                    origin: Pe(t, "top-left"),
                    resolutions: r,
                    tileSize: n,
                  });
                })(ia(t), 0, void 0);
              })(t)),
              t.setDefaultTileGrid(e)),
            e
          );
        }
        function na(t, e, n, i) {
          for (
            var r = void 0 !== e ? e : 42,
              o = Fe(t),
              s = ke(t),
              a = kr(void 0 !== n ? n : 256),
              l = i > 0 ? i : Math.max(s / a[0], o / a[1]),
              h = r + 1,
              u = new Array(h),
              c = 0;
            c < h;
            ++c
          )
            u[c] = l / Math.pow(2, c);
          return u;
        }
        function ia(t) {
          var e = (t = Ue(t)).getExtent();
          if (!e) {
            var n = (180 * Bt[Kt.DEGREES]) / t.getMetersPerUnit();
            e = ve(-n, -n, n, n);
          }
          return e;
        }
        var ra = (function () {
            var t = function (e, n) {
              return (
                (t =
                  Object.setPrototypeOf ||
                  ({ __proto__: [] } instanceof Array &&
                    function (t, e) {
                      t.__proto__ = e;
                    }) ||
                  function (t, e) {
                    for (var n in e)
                      Object.prototype.hasOwnProperty.call(e, n) &&
                        (t[n] = e[n]);
                  }),
                t(e, n)
              );
            };
            return function (e, n) {
              if ("function" != typeof n && null !== n)
                throw new TypeError(
                  "Class extends value " +
                    String(n) +
                    " is not a constructor or null"
                );
              function i() {
                this.constructor = e;
              }
              t(e, n),
                (e.prototype =
                  null === n
                    ? Object.create(n)
                    : ((i.prototype = n.prototype), new i()));
            };
          })(),
          oa = (function (t) {
            function e(e) {
              var n =
                t.call(this, {
                  attributions: e.attributions,
                  attributionsCollapsible: e.attributionsCollapsible,
                  projection: e.projection,
                  state: e.state,
                  wrapX: e.wrapX,
                  interpolate: e.interpolate,
                }) || this;
              return (
                n.on,
                n.once,
                n.un,
                (n.opaque_ = void 0 !== e.opaque && e.opaque),
                (n.tilePixelRatio_ =
                  void 0 !== e.tilePixelRatio ? e.tilePixelRatio : 1),
                (n.tileGrid = void 0 !== e.tileGrid ? e.tileGrid : null),
                n.tileGrid &&
                  kr(
                    n.tileGrid.getTileSize(n.tileGrid.getMinZoom()),
                    [256, 256]
                  ),
                (n.tileCache = new Hs(e.cacheSize || 0)),
                (n.tmpSize = [0, 0]),
                (n.key_ = e.key || ""),
                (n.tileOptions = {
                  transition: e.transition,
                  interpolate: e.interpolate,
                }),
                (n.zDirection = e.zDirection ? e.zDirection : 0),
                n
              );
            }
            return (
              ra(e, t),
              (e.prototype.canExpireCache = function () {
                return this.tileCache.canExpireCache();
              }),
              (e.prototype.expireCache = function (t, e) {
                var n = this.getTileCacheForProjection(t);
                n && n.expireCache(e);
              }),
              (e.prototype.forEachLoadedTile = function (t, e, n, i) {
                var r = this.getTileCacheForProjection(t);
                if (!r) return !1;
                for (var o, s, a, l = !0, h = n.minX; h <= n.maxX; ++h)
                  for (var u = n.minY; u <= n.maxY; ++u)
                    (s = Ks(e, h, u)),
                      (a = !1),
                      r.containsKey(s) &&
                        (a = 2 === (o = r.get(s)).getState()) &&
                        (a = !1 !== i(o)),
                      a || (l = !1);
                return l;
              }),
              (e.prototype.getGutterForProjection = function (t) {
                return 0;
              }),
              (e.prototype.getKey = function () {
                return this.key_;
              }),
              (e.prototype.setKey = function (t) {
                this.key_ !== t && ((this.key_ = t), this.changed());
              }),
              (e.prototype.getOpaque = function (t) {
                return this.opaque_;
              }),
              (e.prototype.getResolutions = function () {
                return this.tileGrid ? this.tileGrid.getResolutions() : null;
              }),
              (e.prototype.getTile = function (t, e, n, i, r) {
                return L();
              }),
              (e.prototype.getTileGrid = function () {
                return this.tileGrid;
              }),
              (e.prototype.getTileGridForProjection = function (t) {
                return this.tileGrid ? this.tileGrid : ea(t);
              }),
              (e.prototype.getTileCacheForProjection = function (t) {
                return vt(Qe(this.getProjection(), t), 68), this.tileCache;
              }),
              (e.prototype.getTilePixelRatio = function (t) {
                return this.tilePixelRatio_;
              }),
              (e.prototype.getTilePixelSize = function (t, e, n) {
                var i,
                  r,
                  o,
                  s = this.getTileGridForProjection(n),
                  a = this.getTilePixelRatio(e),
                  l = kr(s.getTileSize(t), this.tmpSize);
                return 1 == a
                  ? l
                  : ((i = l),
                    (r = a),
                    void 0 === (o = this.tmpSize) && (o = [0, 0]),
                    (o[0] = (i[0] * r + 0.5) | 0),
                    (o[1] = (i[1] * r + 0.5) | 0),
                    o);
              }),
              (e.prototype.getTileCoordForTileUrlFunction = function (t, e) {
                var n = void 0 !== e ? e : this.getProjection(),
                  i = this.getTileGridForProjection(n);
                return (
                  this.getWrapX() &&
                    n.isGlobal() &&
                    (t = (function (t, e, n) {
                      var i = e[0],
                        r = t.getTileCoordCenter(e),
                        o = ia(n);
                      if (de(o, r)) return e;
                      var s = ke(o),
                        a = Math.ceil((o[0] - r[0]) / s);
                      return (r[0] += s * a), t.getTileCoordForCoordAndZ(r, i);
                    })(i, t, n)),
                  (function (t, e) {
                    var n = t[0],
                      i = t[1],
                      r = t[2];
                    if (e.getMinZoom() > n || n > e.getMaxZoom()) return !1;
                    var o = e.getFullTileRange(n);
                    return !o || o.containsXY(i, r);
                  })(t, i)
                    ? t
                    : null
                );
              }),
              (e.prototype.clear = function () {
                this.tileCache.clear();
              }),
              (e.prototype.refresh = function () {
                this.clear(), t.prototype.refresh.call(this);
              }),
              (e.prototype.updateCacheSize = function (t, e) {
                var n = this.getTileCacheForProjection(e);
                t > n.highWaterMark && (n.highWaterMark = t);
              }),
              (e.prototype.useTile = function (t, e, n, i) {}),
              e
            );
          })(Qs),
          sa = (function (t) {
            function e(e, n) {
              var i = t.call(this, e) || this;
              return (i.tile = n), i;
            }
            return ra(e, t), e;
          })(t),
          aa = oa;
        function la(t, e) {
          var n = /\{z\}/g,
            i = /\{x\}/g,
            r = /\{y\}/g,
            o = /\{-y\}/g;
          return function (s, a, l) {
            return s
              ? t
                  .replace(n, s[0].toString())
                  .replace(i, s[1].toString())
                  .replace(r, s[2].toString())
                  .replace(o, function () {
                    var t = s[0],
                      n = e.getFullTileRange(t);
                    return vt(n, 55), (n.getHeight() - s[2] - 1).toString();
                  })
              : void 0;
          };
        }
        var ha = (function () {
            var t = function (e, n) {
              return (
                (t =
                  Object.setPrototypeOf ||
                  ({ __proto__: [] } instanceof Array &&
                    function (t, e) {
                      t.__proto__ = e;
                    }) ||
                  function (t, e) {
                    for (var n in e)
                      Object.prototype.hasOwnProperty.call(e, n) &&
                        (t[n] = e[n]);
                  }),
                t(e, n)
              );
            };
            return function (e, n) {
              if ("function" != typeof n && null !== n)
                throw new TypeError(
                  "Class extends value " +
                    String(n) +
                    " is not a constructor or null"
                );
              function i() {
                this.constructor = e;
              }
              t(e, n),
                (e.prototype =
                  null === n
                    ? Object.create(n)
                    : ((i.prototype = n.prototype), new i()));
            };
          })(),
          ua = (function (t) {
            function e(n) {
              var i =
                t.call(this, {
                  attributions: n.attributions,
                  cacheSize: n.cacheSize,
                  opaque: n.opaque,
                  projection: n.projection,
                  state: n.state,
                  tileGrid: n.tileGrid,
                  tilePixelRatio: n.tilePixelRatio,
                  wrapX: n.wrapX,
                  transition: n.transition,
                  interpolate: n.interpolate,
                  key: n.key,
                  attributionsCollapsible: n.attributionsCollapsible,
                  zDirection: n.zDirection,
                }) || this;
              return (
                (i.generateTileUrlFunction_ =
                  i.tileUrlFunction === e.prototype.tileUrlFunction),
                (i.tileLoadFunction = n.tileLoadFunction),
                n.tileUrlFunction && (i.tileUrlFunction = n.tileUrlFunction),
                (i.urls = null),
                n.urls ? i.setUrls(n.urls) : n.url && i.setUrl(n.url),
                (i.tileLoadingKeys_ = {}),
                i
              );
            }
            return (
              ha(e, t),
              (e.prototype.getTileLoadFunction = function () {
                return this.tileLoadFunction;
              }),
              (e.prototype.getTileUrlFunction = function () {
                return Object.getPrototypeOf(this).tileUrlFunction ===
                  this.tileUrlFunction
                  ? this.tileUrlFunction.bind(this)
                  : this.tileUrlFunction;
              }),
              (e.prototype.getUrls = function () {
                return this.urls;
              }),
              (e.prototype.handleTileChange = function (t) {
                var e,
                  n = t.target,
                  i = D(n),
                  r = n.getState();
                1 == r
                  ? ((this.tileLoadingKeys_[i] = !0), (e = "tileloadstart"))
                  : i in this.tileLoadingKeys_ &&
                    (delete this.tileLoadingKeys_[i],
                    (e =
                      3 == r
                        ? "tileloaderror"
                        : 2 == r
                        ? "tileloadend"
                        : void 0)),
                  null != e && this.dispatchEvent(new sa(e, n));
              }),
              (e.prototype.setTileLoadFunction = function (t) {
                this.tileCache.clear(),
                  (this.tileLoadFunction = t),
                  this.changed();
              }),
              (e.prototype.setTileUrlFunction = function (t, e) {
                (this.tileUrlFunction = t),
                  this.tileCache.pruneExceptNewestZ(),
                  void 0 !== e ? this.setKey(e) : this.changed();
              }),
              (e.prototype.setUrl = function (t) {
                var e = (function (t) {
                  var e = [],
                    n = /\{([a-z])-([a-z])\}/.exec(t);
                  if (n) {
                    var i = n[1].charCodeAt(0),
                      r = n[2].charCodeAt(0),
                      o = void 0;
                    for (o = i; o <= r; ++o)
                      e.push(t.replace(n[0], String.fromCharCode(o)));
                    return e;
                  }
                  if ((n = /\{(\d+)-(\d+)\}/.exec(t))) {
                    for (
                      var s = parseInt(n[2], 10), a = parseInt(n[1], 10);
                      a <= s;
                      a++
                    )
                      e.push(t.replace(n[0], a.toString()));
                    return e;
                  }
                  return e.push(t), e;
                })(t);
                (this.urls = e), this.setUrls(e);
              }),
              (e.prototype.setUrls = function (t) {
                this.urls = t;
                var e = t.join("\n");
                this.generateTileUrlFunction_
                  ? this.setTileUrlFunction(
                      (function (t, e) {
                        for (
                          var n = t.length, i = new Array(n), r = 0;
                          r < n;
                          ++r
                        )
                          i[r] = la(t[r], e);
                        return (function (t) {
                          return 1 === t.length
                            ? t[0]
                            : function (e, n, i) {
                                if (e) {
                                  var r = (function (t) {
                                      return (t[1] << t[0]) + t[2];
                                    })(e),
                                    o = Tt(r, t.length);
                                  return t[o](e, n, i);
                                }
                              };
                        })(i);
                      })(t, this.tileGrid),
                      e
                    )
                  : this.setKey(e);
              }),
              (e.prototype.tileUrlFunction = function (t, e, n) {}),
              (e.prototype.useTile = function (t, e, n) {
                var i = Ks(t, e, n);
                this.tileCache.containsKey(i) && this.tileCache.get(i);
              }),
              e
            );
          })(aa),
          ca = (function () {
            var t = function (e, n) {
              return (
                (t =
                  Object.setPrototypeOf ||
                  ({ __proto__: [] } instanceof Array &&
                    function (t, e) {
                      t.__proto__ = e;
                    }) ||
                  function (t, e) {
                    for (var n in e)
                      Object.prototype.hasOwnProperty.call(e, n) &&
                        (t[n] = e[n]);
                  }),
                t(e, n)
              );
            };
            return function (e, n) {
              if ("function" != typeof n && null !== n)
                throw new TypeError(
                  "Class extends value " +
                    String(n) +
                    " is not a constructor or null"
                );
              function i() {
                this.constructor = e;
              }
              t(e, n),
                (e.prototype =
                  null === n
                    ? Object.create(n)
                    : ((i.prototype = n.prototype), new i()));
            };
          })(),
          pa = (function (t) {
            function e(e) {
              var n = this,
                i = void 0 === e.imageSmoothing || e.imageSmoothing;
              return (
                void 0 !== e.interpolate && (i = e.interpolate),
                ((n =
                  t.call(this, {
                    attributions: e.attributions,
                    cacheSize: e.cacheSize,
                    opaque: e.opaque,
                    projection: e.projection,
                    state: e.state,
                    tileGrid: e.tileGrid,
                    tileLoadFunction: e.tileLoadFunction
                      ? e.tileLoadFunction
                      : fa,
                    tilePixelRatio: e.tilePixelRatio,
                    tileUrlFunction: e.tileUrlFunction,
                    url: e.url,
                    urls: e.urls,
                    wrapX: e.wrapX,
                    transition: e.transition,
                    interpolate: i,
                    key: e.key,
                    attributionsCollapsible: e.attributionsCollapsible,
                    zDirection: e.zDirection,
                  }) || this).crossOrigin =
                  void 0 !== e.crossOrigin ? e.crossOrigin : null),
                (n.tileClass = void 0 !== e.tileClass ? e.tileClass : Sr),
                (n.tileCacheForProjection = {}),
                (n.tileGridForProjection = {}),
                (n.reprojectionErrorThreshold_ = e.reprojectionErrorThreshold),
                (n.renderReprojectionEdges_ = !1),
                n
              );
            }
            return (
              ca(e, t),
              (e.prototype.canExpireCache = function () {
                if (this.tileCache.canExpireCache()) return !0;
                for (var t in this.tileCacheForProjection)
                  if (this.tileCacheForProjection[t].canExpireCache())
                    return !0;
                return !1;
              }),
              (e.prototype.expireCache = function (t, e) {
                var n = this.getTileCacheForProjection(t);
                for (var i in (this.tileCache.expireCache(
                  this.tileCache == n ? e : {}
                ),
                this.tileCacheForProjection)) {
                  var r = this.tileCacheForProjection[i];
                  r.expireCache(r == n ? e : {});
                }
              }),
              (e.prototype.getGutterForProjection = function (t) {
                return this.getProjection() && t && !Qe(this.getProjection(), t)
                  ? 0
                  : this.getGutter();
              }),
              (e.prototype.getGutter = function () {
                return 0;
              }),
              (e.prototype.getKey = function () {
                var e = t.prototype.getKey.call(this);
                return (
                  this.getInterpolate() || (e += ":disable-interpolation"), e
                );
              }),
              (e.prototype.getOpaque = function (e) {
                return (
                  !(
                    this.getProjection() &&
                    e &&
                    !Qe(this.getProjection(), e)
                  ) && t.prototype.getOpaque.call(this, e)
                );
              }),
              (e.prototype.getTileGridForProjection = function (t) {
                var e = this.getProjection();
                if (!this.tileGrid || (e && !Qe(e, t))) {
                  var n = D(t);
                  return (
                    n in this.tileGridForProjection ||
                      (this.tileGridForProjection[n] = ea(t)),
                    this.tileGridForProjection[n]
                  );
                }
                return this.tileGrid;
              }),
              (e.prototype.getTileCacheForProjection = function (t) {
                var e = this.getProjection();
                if (!e || Qe(e, t)) return this.tileCache;
                var n = D(t);
                return (
                  n in this.tileCacheForProjection ||
                    (this.tileCacheForProjection[n] = new Hs(
                      this.tileCache.highWaterMark
                    )),
                  this.tileCacheForProjection[n]
                );
              }),
              (e.prototype.createTile_ = function (t, e, n, i, r, o) {
                var s = [t, e, n],
                  a = this.getTileCoordForTileUrlFunction(s, r),
                  l = a ? this.tileUrlFunction(a, i, r) : void 0,
                  h = new this.tileClass(
                    s,
                    void 0 !== l ? 0 : 4,
                    void 0 !== l ? l : "",
                    this.crossOrigin,
                    this.tileLoadFunction,
                    this.tileOptions
                  );
                return (
                  (h.key = o),
                  h.addEventListener(x, this.handleTileChange.bind(this)),
                  h
                );
              }),
              (e.prototype.getTile = function (t, e, n, i, r) {
                var o = this.getProjection();
                if (o && r && !Qe(o, r)) {
                  var s = this.getTileCacheForProjection(r),
                    a = [t, e, n],
                    l = void 0,
                    h = Zs(a);
                  s.containsKey(h) && (l = s.get(h));
                  var u = this.getKey();
                  if (l && l.key == u) return l;
                  var c = this.getTileGridForProjection(o),
                    p = this.getTileGridForProjection(r),
                    f = this.getTileCoordForTileUrlFunction(a, r),
                    d = new Mr(
                      o,
                      c,
                      r,
                      p,
                      a,
                      f,
                      this.getTilePixelRatio(i),
                      this.getGutter(),
                      function (t, e, n, i) {
                        return this.getTileInternal(t, e, n, i, o);
                      }.bind(this),
                      this.reprojectionErrorThreshold_,
                      this.renderReprojectionEdges_,
                      this.getInterpolate()
                    );
                  return (
                    (d.key = u),
                    l
                      ? ((d.interimTile = l),
                        d.refreshInterimChain(),
                        s.replace(h, d))
                      : s.set(h, d),
                    d
                  );
                }
                return this.getTileInternal(t, e, n, i, o || r);
              }),
              (e.prototype.getTileInternal = function (t, e, n, i, r) {
                var o = null,
                  s = Ks(t, e, n),
                  a = this.getKey();
                if (this.tileCache.containsKey(s)) {
                  if ((o = this.tileCache.get(s)).key != a) {
                    var l = o;
                    (o = this.createTile_(t, e, n, i, r, a)),
                      0 == l.getState()
                        ? (o.interimTile = l.interimTile)
                        : (o.interimTile = l),
                      o.refreshInterimChain(),
                      this.tileCache.replace(s, o);
                  }
                } else
                  (o = this.createTile_(t, e, n, i, r, a)),
                    this.tileCache.set(s, o);
                return o;
              }),
              (e.prototype.setRenderReprojectionEdges = function (t) {
                if (this.renderReprojectionEdges_ != t) {
                  for (var e in ((this.renderReprojectionEdges_ = t),
                  this.tileCacheForProjection))
                    this.tileCacheForProjection[e].clear();
                  this.changed();
                }
              }),
              (e.prototype.setTileGridForProjection = function (t, e) {
                var n = Ue(t);
                if (n) {
                  var i = D(n);
                  i in this.tileGridForProjection ||
                    (this.tileGridForProjection[i] = e);
                }
              }),
              e
            );
          })(ua);
        function fa(t, e) {
          t.getImage().src = e;
        }
        var da = pa,
          ga = (function () {
            var t = function (e, n) {
              return (
                (t =
                  Object.setPrototypeOf ||
                  ({ __proto__: [] } instanceof Array &&
                    function (t, e) {
                      t.__proto__ = e;
                    }) ||
                  function (t, e) {
                    for (var n in e)
                      Object.prototype.hasOwnProperty.call(e, n) &&
                        (t[n] = e[n]);
                  }),
                t(e, n)
              );
            };
            return function (e, n) {
              if ("function" != typeof n && null !== n)
                throw new TypeError(
                  "Class extends value " +
                    String(n) +
                    " is not a constructor or null"
                );
              function i() {
                this.constructor = e;
              }
              t(e, n),
                (e.prototype =
                  null === n
                    ? Object.create(n)
                    : ((i.prototype = n.prototype), new i()));
            };
          })(),
          _a = (function (t) {
            function e(e) {
              var n = e || {},
                i = void 0 === n.imageSmoothing || n.imageSmoothing;
              void 0 !== n.interpolate && (i = n.interpolate);
              var r = void 0 !== n.projection ? n.projection : "EPSG:3857",
                o =
                  void 0 !== n.tileGrid
                    ? n.tileGrid
                    : (function (t) {
                        var e = t || {},
                          n = e.extent || Ue("EPSG:3857").getExtent(),
                          i = {
                            extent: n,
                            minZoom: e.minZoom,
                            tileSize: e.tileSize,
                            resolutions: na(
                              n,
                              e.maxZoom,
                              e.tileSize,
                              e.maxResolution
                            ),
                          };
                        return new ta(i);
                      })({
                        extent: ia(r),
                        maxResolution: n.maxResolution,
                        maxZoom: n.maxZoom,
                        minZoom: n.minZoom,
                        tileSize: n.tileSize,
                      });
              return (
                t.call(this, {
                  attributions: n.attributions,
                  cacheSize: n.cacheSize,
                  crossOrigin: n.crossOrigin,
                  interpolate: i,
                  opaque: n.opaque,
                  projection: r,
                  reprojectionErrorThreshold: n.reprojectionErrorThreshold,
                  tileGrid: o,
                  tileLoadFunction: n.tileLoadFunction,
                  tilePixelRatio: n.tilePixelRatio,
                  tileUrlFunction: n.tileUrlFunction,
                  url: n.url,
                  urls: n.urls,
                  wrapX: void 0 === n.wrapX || n.wrapX,
                  transition: n.transition,
                  attributionsCollapsible: n.attributionsCollapsible,
                  zDirection: n.zDirection,
                }) || this
              );
            }
            return ga(e, t), e;
          })(da),
          ya = (function () {
            var t = function (e, n) {
              return (
                (t =
                  Object.setPrototypeOf ||
                  ({ __proto__: [] } instanceof Array &&
                    function (t, e) {
                      t.__proto__ = e;
                    }) ||
                  function (t, e) {
                    for (var n in e)
                      Object.prototype.hasOwnProperty.call(e, n) &&
                        (t[n] = e[n]);
                  }),
                t(e, n)
              );
            };
            return function (e, n) {
              if ("function" != typeof n && null !== n)
                throw new TypeError(
                  "Class extends value " +
                    String(n) +
                    " is not a constructor or null"
                );
              function i() {
                this.constructor = e;
              }
              t(e, n),
                (e.prototype =
                  null === n
                    ? Object.create(n)
                    : ((i.prototype = n.prototype), new i()));
            };
          })(),
          va = (function (t) {
            function e(e) {
              var n,
                i = e || {},
                r = void 0 === i.imageSmoothing || i.imageSmoothing;
              void 0 !== i.interpolate && (r = i.interpolate),
                (n =
                  void 0 !== i.attributions
                    ? i.attributions
                    : [
                        '&#169; <a href="https://www.openstreetmap.org/copyright" target="_blank">OpenStreetMap</a> contributors.',
                      ]);
              var o = void 0 !== i.crossOrigin ? i.crossOrigin : "anonymous",
                s =
                  void 0 !== i.url
                    ? i.url
                    : "https://{a-c}.tile.openstreetmap.org/{z}/{x}/{y}.png";
              return (
                t.call(this, {
                  attributions: n,
                  attributionsCollapsible: !1,
                  cacheSize: i.cacheSize,
                  crossOrigin: o,
                  interpolate: r,
                  maxZoom: void 0 !== i.maxZoom ? i.maxZoom : 19,
                  opaque: void 0 === i.opaque || i.opaque,
                  reprojectionErrorThreshold: i.reprojectionErrorThreshold,
                  tileLoadFunction: i.tileLoadFunction,
                  transition: i.transition,
                  url: s,
                  wrapX: i.wrapX,
                  zDirection: i.zDirection,
                }) || this
              );
            }
            return ya(e, t), e;
          })(_a),
          ma = "add",
          xa = "remove",
          Ca = (function () {
            var t = function (e, n) {
              return (
                (t =
                  Object.setPrototypeOf ||
                  ({ __proto__: [] } instanceof Array &&
                    function (t, e) {
                      t.__proto__ = e;
                    }) ||
                  function (t, e) {
                    for (var n in e)
                      Object.prototype.hasOwnProperty.call(e, n) &&
                        (t[n] = e[n]);
                  }),
                t(e, n)
              );
            };
            return function (e, n) {
              if ("function" != typeof n && null !== n)
                throw new TypeError(
                  "Class extends value " +
                    String(n) +
                    " is not a constructor or null"
                );
              function i() {
                this.constructor = e;
              }
              t(e, n),
                (e.prototype =
                  null === n
                    ? Object.create(n)
                    : ((i.prototype = n.prototype), new i()));
            };
          })(),
          wa = "length",
          Sa = (function (t) {
            function e(e, n, i) {
              var r = t.call(this, e) || this;
              return (r.element = n), (r.index = i), r;
            }
            return Ca(e, t), e;
          })(t),
          Ea = (function (t) {
            function e(e, n) {
              var i = t.call(this) || this;
              i.on, i.once, i.un;
              var r = n || {};
              if (((i.unique_ = !!r.unique), (i.array_ = e || []), i.unique_))
                for (var o = 0, s = i.array_.length; o < s; ++o)
                  i.assertUnique_(i.array_[o], o);
              return i.updateLength_(), i;
            }
            return (
              Ca(e, t),
              (e.prototype.clear = function () {
                for (; this.getLength() > 0; ) this.pop();
              }),
              (e.prototype.extend = function (t) {
                for (var e = 0, n = t.length; e < n; ++e) this.push(t[e]);
                return this;
              }),
              (e.prototype.forEach = function (t) {
                for (var e = this.array_, n = 0, i = e.length; n < i; ++n)
                  t(e[n], n, e);
              }),
              (e.prototype.getArray = function () {
                return this.array_;
              }),
              (e.prototype.item = function (t) {
                return this.array_[t];
              }),
              (e.prototype.getLength = function () {
                return this.get(wa);
              }),
              (e.prototype.insertAt = function (t, e) {
                this.unique_ && this.assertUnique_(e),
                  this.array_.splice(t, 0, e),
                  this.updateLength_(),
                  this.dispatchEvent(new Sa(ma, e, t));
              }),
              (e.prototype.pop = function () {
                return this.removeAt(this.getLength() - 1);
              }),
              (e.prototype.push = function (t) {
                this.unique_ && this.assertUnique_(t);
                var e = this.getLength();
                return this.insertAt(e, t), this.getLength();
              }),
              (e.prototype.remove = function (t) {
                for (var e = this.array_, n = 0, i = e.length; n < i; ++n)
                  if (e[n] === t) return this.removeAt(n);
              }),
              (e.prototype.removeAt = function (t) {
                var e = this.array_[t];
                return (
                  this.array_.splice(t, 1),
                  this.updateLength_(),
                  this.dispatchEvent(new Sa(xa, e, t)),
                  e
                );
              }),
              (e.prototype.setAt = function (t, e) {
                var n = this.getLength();
                if (t < n) {
                  this.unique_ && this.assertUnique_(e, t);
                  var i = this.array_[t];
                  (this.array_[t] = e),
                    this.dispatchEvent(new Sa(xa, i, t)),
                    this.dispatchEvent(new Sa(ma, e, t));
                } else {
                  for (var r = n; r < t; ++r) this.insertAt(r, void 0);
                  this.insertAt(t, e);
                }
              }),
              (e.prototype.updateLength_ = function () {
                this.set(wa, this.array_.length);
              }),
              (e.prototype.assertUnique_ = function (t, e) {
                for (var n = 0, i = this.array_.length; n < i; ++n)
                  if (this.array_[n] === t && n !== e) throw new yt(58);
              }),
              e
            );
          })(G),
          Ta = (function () {
            function t(t) {
              (this.rbush_ = new Nr(t)), (this.items_ = {});
            }
            return (
              (t.prototype.insert = function (t, e) {
                var n = {
                  minX: t[0],
                  minY: t[1],
                  maxX: t[2],
                  maxY: t[3],
                  value: e,
                };
                this.rbush_.insert(n), (this.items_[D(e)] = n);
              }),
              (t.prototype.load = function (t, e) {
                for (
                  var n = new Array(e.length), i = 0, r = e.length;
                  i < r;
                  i++
                ) {
                  var o = t[i],
                    s = e[i],
                    a = {
                      minX: o[0],
                      minY: o[1],
                      maxX: o[2],
                      maxY: o[3],
                      value: s,
                    };
                  (n[i] = a), (this.items_[D(s)] = a);
                }
                this.rbush_.load(n);
              }),
              (t.prototype.remove = function (t) {
                var e = D(t),
                  n = this.items_[e];
                return delete this.items_[e], null !== this.rbush_.remove(n);
              }),
              (t.prototype.update = function (t, e) {
                var n = this.items_[D(e)];
                Ce([n.minX, n.minY, n.maxX, n.maxY], t) ||
                  (this.remove(e), this.insert(t, e));
              }),
              (t.prototype.getAll = function () {
                return this.rbush_.all().map(function (t) {
                  return t.value;
                });
              }),
              (t.prototype.getInExtent = function (t) {
                var e = { minX: t[0], minY: t[1], maxX: t[2], maxY: t[3] };
                return this.rbush_.search(e).map(function (t) {
                  return t.value;
                });
              }),
              (t.prototype.forEach = function (t) {
                return this.forEach_(this.getAll(), t);
              }),
              (t.prototype.forEachInExtent = function (t, e) {
                return this.forEach_(this.getInExtent(t), e);
              }),
              (t.prototype.forEach_ = function (t, e) {
                for (var n, i = 0, r = t.length; i < r; i++)
                  if ((n = e(t[i]))) return n;
                return n;
              }),
              (t.prototype.isEmpty = function () {
                return _(this.items_);
              }),
              (t.prototype.clear = function () {
                this.rbush_.clear(), (this.items_ = {});
              }),
              (t.prototype.getExtent = function (t) {
                var e = this.rbush_.toJSON();
                return ve(e.minX, e.minY, e.maxX, e.maxY, t);
              }),
              (t.prototype.concat = function (t) {
                for (var e in (this.rbush_.load(t.rbush_.all()), t.items_))
                  this.items_[e] = t.items_[e];
              }),
              t
            );
          })(),
          ba = "addfeature",
          Oa = "removefeature";
        function Ra(t, e) {
          return [[-1 / 0, -1 / 0, 1 / 0, 1 / 0]];
        }
        var Ia = "arraybuffer";
        function Pa(t, e) {
          return function (n, i, r, o, s) {
            var a = this;
            !(function (t, e, n, i, r, o, s) {
              var a = new XMLHttpRequest();
              a.open("GET", "function" == typeof t ? t(n, i, r) : t, !0),
                e.getType() == Ia && (a.responseType = "arraybuffer"),
                (a.withCredentials = false),
                (a.onload = function (t) {
                  if (!a.status || (a.status >= 200 && a.status < 300)) {
                    var i = e.getType(),
                      l = void 0;
                    "json" == i || "text" == i
                      ? (l = a.responseText)
                      : "xml" == i
                      ? (l = a.responseXML) ||
                        (l = new DOMParser().parseFromString(
                          a.responseText,
                          "application/xml"
                        ))
                      : i == Ia && (l = a.response),
                      l
                        ? o(
                            e.readFeatures(l, {
                              extent: n,
                              featureProjection: r,
                            }),
                            e.readProjection(l)
                          )
                        : s();
                  } else s();
                }),
                (a.onerror = s),
                a.send();
            })(
              t,
              e,
              n,
              i,
              r,
              function (t, e) {
                a.addFeatures(t), void 0 !== o && o(t);
              },
              s || p
            );
          };
        }
        var Ma = (function () {
            var t = function (e, n) {
              return (
                (t =
                  Object.setPrototypeOf ||
                  ({ __proto__: [] } instanceof Array &&
                    function (t, e) {
                      t.__proto__ = e;
                    }) ||
                  function (t, e) {
                    for (var n in e)
                      Object.prototype.hasOwnProperty.call(e, n) &&
                        (t[n] = e[n]);
                  }),
                t(e, n)
              );
            };
            return function (e, n) {
              if ("function" != typeof n && null !== n)
                throw new TypeError(
                  "Class extends value " +
                    String(n) +
                    " is not a constructor or null"
                );
              function i() {
                this.constructor = e;
              }
              t(e, n),
                (e.prototype =
                  null === n
                    ? Object.create(n)
                    : ((i.prototype = n.prototype), new i()));
            };
          })(),
          Fa = (function (t) {
            function e(e, n, i) {
              var r = t.call(this, e) || this;
              return (r.feature = n), (r.features = i), r;
            }
            return Ma(e, t), e;
          })(t),
          La = (function (t) {
            function n(e) {
              var n = this,
                i = e || {};
              (n =
                t.call(this, {
                  attributions: i.attributions,
                  interpolate: !0,
                  projection: void 0,
                  state: Dt,
                  wrapX: void 0 === i.wrapX || i.wrapX,
                }) || this).on,
                n.once,
                n.un,
                (n.loader_ = p),
                (n.format_ = i.format),
                (n.overlaps_ = void 0 === i.overlaps || i.overlaps),
                (n.url_ = i.url),
                void 0 !== i.loader
                  ? (n.loader_ = i.loader)
                  : void 0 !== n.url_ &&
                    (vt(n.format_, 7), (n.loader_ = Pa(n.url_, n.format_))),
                (n.strategy_ = void 0 !== i.strategy ? i.strategy : Ra);
              var r,
                o,
                s = void 0 === i.useSpatialIndex || i.useSpatialIndex;
              return (
                (n.featuresRtree_ = s ? new Ta() : null),
                (n.loadedExtentsRtree_ = new Ta()),
                (n.loadingExtentsCount_ = 0),
                (n.nullGeometryFeatures_ = {}),
                (n.idIndex_ = {}),
                (n.uidIndex_ = {}),
                (n.featureChangeKeys_ = {}),
                (n.featuresCollection_ = null),
                Array.isArray(i.features)
                  ? (o = i.features)
                  : i.features && (o = (r = i.features).getArray()),
                s || void 0 !== r || (r = new Ea(o)),
                void 0 !== o && n.addFeaturesInternal(o),
                void 0 !== r && n.bindFeaturesCollection_(r),
                n
              );
            }
            return (
              Ma(n, t),
              (n.prototype.addFeature = function (t) {
                this.addFeatureInternal(t), this.changed();
              }),
              (n.prototype.addFeatureInternal = function (t) {
                var e = D(t);
                if (this.addToIndex_(e, t)) {
                  this.setupChangeEvents_(e, t);
                  var n = t.getGeometry();
                  if (n) {
                    var i = n.getExtent();
                    this.featuresRtree_ && this.featuresRtree_.insert(i, t);
                  } else this.nullGeometryFeatures_[e] = t;
                  this.dispatchEvent(new Fa(ba, t));
                } else
                  this.featuresCollection_ &&
                    this.featuresCollection_.remove(t);
              }),
              (n.prototype.setupChangeEvents_ = function (t, n) {
                this.featureChangeKeys_[t] = [
                  O(n, x, this.handleFeatureChange_, this),
                  O(n, e, this.handleFeatureChange_, this),
                ];
              }),
              (n.prototype.addToIndex_ = function (t, e) {
                var n = !0,
                  i = e.getId();
                return (
                  void 0 !== i &&
                    (i.toString() in this.idIndex_
                      ? (n = !1)
                      : (this.idIndex_[i.toString()] = e)),
                  n &&
                    (vt(!(t in this.uidIndex_), 30), (this.uidIndex_[t] = e)),
                  n
                );
              }),
              (n.prototype.addFeatures = function (t) {
                this.addFeaturesInternal(t), this.changed();
              }),
              (n.prototype.addFeaturesInternal = function (t) {
                for (
                  var e = [], n = [], i = [], r = 0, o = t.length;
                  r < o;
                  r++
                ) {
                  var s = D((l = t[r]));
                  this.addToIndex_(s, l) && n.push(l);
                }
                r = 0;
                for (var a = n.length; r < a; r++) {
                  var l;
                  (s = D((l = n[r]))), this.setupChangeEvents_(s, l);
                  var h = l.getGeometry();
                  if (h) {
                    var u = h.getExtent();
                    e.push(u), i.push(l);
                  } else this.nullGeometryFeatures_[s] = l;
                }
                if (
                  (this.featuresRtree_ && this.featuresRtree_.load(e, i),
                  this.hasListener(ba))
                ) {
                  r = 0;
                  for (var c = n.length; r < c; r++)
                    this.dispatchEvent(new Fa(ba, n[r]));
                }
              }),
              (n.prototype.bindFeaturesCollection_ = function (t) {
                var e = !1;
                this.addEventListener(ba, function (n) {
                  e || ((e = !0), t.push(n.feature), (e = !1));
                }),
                  this.addEventListener(Oa, function (n) {
                    e || ((e = !0), t.remove(n.feature), (e = !1));
                  }),
                  t.addEventListener(
                    ma,
                    function (t) {
                      e || ((e = !0), this.addFeature(t.element), (e = !1));
                    }.bind(this)
                  ),
                  t.addEventListener(
                    xa,
                    function (t) {
                      e || ((e = !0), this.removeFeature(t.element), (e = !1));
                    }.bind(this)
                  ),
                  (this.featuresCollection_ = t);
              }),
              (n.prototype.clear = function (t) {
                if (t) {
                  for (var e in this.featureChangeKeys_)
                    this.featureChangeKeys_[e].forEach(I);
                  this.featuresCollection_ ||
                    ((this.featureChangeKeys_ = {}),
                    (this.idIndex_ = {}),
                    (this.uidIndex_ = {}));
                } else if (this.featuresRtree_) {
                  var n = function (t) {
                    this.removeFeatureInternal(t);
                  }.bind(this);
                  for (var i in (this.featuresRtree_.forEach(n),
                  this.nullGeometryFeatures_))
                    this.removeFeatureInternal(this.nullGeometryFeatures_[i]);
                }
                this.featuresCollection_ && this.featuresCollection_.clear(),
                  this.featuresRtree_ && this.featuresRtree_.clear(),
                  (this.nullGeometryFeatures_ = {});
                var r = new Fa("clear");
                this.dispatchEvent(r), this.changed();
              }),
              (n.prototype.forEachFeature = function (t) {
                if (this.featuresRtree_) return this.featuresRtree_.forEach(t);
                this.featuresCollection_ && this.featuresCollection_.forEach(t);
              }),
              (n.prototype.forEachFeatureAtCoordinateDirect = function (t, e) {
                var n = [t[0], t[1], t[0], t[1]];
                return this.forEachFeatureInExtent(n, function (n) {
                  return n.getGeometry().intersectsCoordinate(t)
                    ? e(n)
                    : void 0;
                });
              }),
              (n.prototype.forEachFeatureInExtent = function (t, e) {
                if (this.featuresRtree_)
                  return this.featuresRtree_.forEachInExtent(t, e);
                this.featuresCollection_ && this.featuresCollection_.forEach(e);
              }),
              (n.prototype.forEachFeatureIntersectingExtent = function (t, e) {
                return this.forEachFeatureInExtent(t, function (n) {
                  if (n.getGeometry().intersectsExtent(t)) {
                    var i = e(n);
                    if (i) return i;
                  }
                });
              }),
              (n.prototype.getFeaturesCollection = function () {
                return this.featuresCollection_;
              }),
              (n.prototype.getFeatures = function () {
                var t;
                return (
                  this.featuresCollection_
                    ? (t = this.featuresCollection_.getArray().slice(0))
                    : this.featuresRtree_ &&
                      ((t = this.featuresRtree_.getAll()),
                      _(this.nullGeometryFeatures_) ||
                        l(t, g(this.nullGeometryFeatures_))),
                  t
                );
              }),
              (n.prototype.getFeaturesAtCoordinate = function (t) {
                var e = [];
                return (
                  this.forEachFeatureAtCoordinateDirect(t, function (t) {
                    e.push(t);
                  }),
                  e
                );
              }),
              (n.prototype.getFeaturesInExtent = function (t) {
                return this.featuresRtree_
                  ? this.featuresRtree_.getInExtent(t)
                  : this.featuresCollection_
                  ? this.featuresCollection_.getArray().slice(0)
                  : [];
              }),
              (n.prototype.getClosestFeatureToCoordinate = function (t, e) {
                var n = t[0],
                  i = t[1],
                  r = null,
                  o = [NaN, NaN],
                  s = 1 / 0,
                  a = [-1 / 0, -1 / 0, 1 / 0, 1 / 0],
                  l = e || u;
                return (
                  this.featuresRtree_.forEachInExtent(a, function (t) {
                    if (l(t)) {
                      var e = t.getGeometry(),
                        h = s;
                      if ((s = e.closestPointXY(n, i, o, s)) < h) {
                        r = t;
                        var u = Math.sqrt(s);
                        (a[0] = n - u),
                          (a[1] = i - u),
                          (a[2] = n + u),
                          (a[3] = i + u);
                      }
                    }
                  }),
                  r
                );
              }),
              (n.prototype.getExtent = function (t) {
                return this.featuresRtree_.getExtent(t);
              }),
              (n.prototype.getFeatureById = function (t) {
                var e = this.idIndex_[t.toString()];
                return void 0 !== e ? e : null;
              }),
              (n.prototype.getFeatureByUid = function (t) {
                var e = this.uidIndex_[t];
                return void 0 !== e ? e : null;
              }),
              (n.prototype.getFormat = function () {
                return this.format_;
              }),
              (n.prototype.getOverlaps = function () {
                return this.overlaps_;
              }),
              (n.prototype.getUrl = function () {
                return this.url_;
              }),
              (n.prototype.handleFeatureChange_ = function (t) {
                var e = t.target,
                  n = D(e),
                  i = e.getGeometry();
                if (i) {
                  var r = i.getExtent();
                  n in this.nullGeometryFeatures_
                    ? (delete this.nullGeometryFeatures_[n],
                      this.featuresRtree_ && this.featuresRtree_.insert(r, e))
                    : this.featuresRtree_ && this.featuresRtree_.update(r, e);
                } else
                  n in this.nullGeometryFeatures_ ||
                    (this.featuresRtree_ && this.featuresRtree_.remove(e),
                    (this.nullGeometryFeatures_[n] = e));
                var o = e.getId();
                if (void 0 !== o) {
                  var s = o.toString();
                  this.idIndex_[s] !== e &&
                    (this.removeFromIdIndex_(e), (this.idIndex_[s] = e));
                } else this.removeFromIdIndex_(e), (this.uidIndex_[n] = e);
                this.changed(), this.dispatchEvent(new Fa("changefeature", e));
              }),
              (n.prototype.hasFeature = function (t) {
                var e = t.getId();
                return void 0 !== e
                  ? e in this.idIndex_
                  : D(t) in this.uidIndex_;
              }),
              (n.prototype.isEmpty = function () {
                return this.featuresRtree_
                  ? this.featuresRtree_.isEmpty() &&
                      _(this.nullGeometryFeatures_)
                  : !this.featuresCollection_ ||
                      0 === this.featuresCollection_.getLength();
              }),
              (n.prototype.loadFeatures = function (t, e, n) {
                for (
                  var i = this.loadedExtentsRtree_,
                    r = this.strategy_(t, e, n),
                    o = function (t, o) {
                      var a = r[t];
                      i.forEachInExtent(a, function (t) {
                        return ge(t.extent, a);
                      }) ||
                        (++s.loadingExtentsCount_,
                        s.dispatchEvent(new Fa("featuresloadstart")),
                        s.loader_.call(
                          s,
                          a,
                          e,
                          n,
                          function (t) {
                            --this.loadingExtentsCount_,
                              this.dispatchEvent(
                                new Fa("featuresloadend", void 0, t)
                              );
                          }.bind(s),
                          function () {
                            --this.loadingExtentsCount_,
                              this.dispatchEvent(new Fa("featuresloaderror"));
                          }.bind(s)
                        ),
                        i.insert(a, { extent: a.slice() }));
                    },
                    s = this,
                    a = 0,
                    l = r.length;
                  a < l;
                  ++a
                )
                  o(a);
                this.loading =
                  !(this.loader_.length < 4) && this.loadingExtentsCount_ > 0;
              }),
              (n.prototype.refresh = function () {
                this.clear(!0),
                  this.loadedExtentsRtree_.clear(),
                  t.prototype.refresh.call(this);
              }),
              (n.prototype.removeLoadedExtent = function (t) {
                var e,
                  n = this.loadedExtentsRtree_;
                n.forEachInExtent(t, function (n) {
                  if (Ce(n.extent, t)) return (e = n), !0;
                }),
                  e && n.remove(e);
              }),
              (n.prototype.removeFeature = function (t) {
                if (t) {
                  var e = D(t);
                  e in this.nullGeometryFeatures_
                    ? delete this.nullGeometryFeatures_[e]
                    : this.featuresRtree_ && this.featuresRtree_.remove(t),
                    this.removeFeatureInternal(t) && this.changed();
                }
              }),
              (n.prototype.removeFeatureInternal = function (t) {
                var e = D(t),
                  n = this.featureChangeKeys_[e];
                if (n) {
                  n.forEach(I), delete this.featureChangeKeys_[e];
                  var i = t.getId();
                  return (
                    void 0 !== i && delete this.idIndex_[i.toString()],
                    delete this.uidIndex_[e],
                    this.dispatchEvent(new Fa(Oa, t)),
                    t
                  );
                }
              }),
              (n.prototype.removeFromIdIndex_ = function (t) {
                var e = !1;
                for (var n in this.idIndex_)
                  if (this.idIndex_[n] === t) {
                    delete this.idIndex_[n], (e = !0);
                    break;
                  }
                return e;
              }),
              (n.prototype.setLoader = function (t) {
                this.loader_ = t;
              }),
              (n.prototype.setUrl = function (t) {
                vt(this.format_, 7),
                  (this.url_ = t),
                  this.setLoader(Pa(t, this.format_));
              }),
              n
            );
          })(Qs),
          Aa = (function () {
            function t(t) {
              var e = t || {};
              (this.font_ = e.font),
                (this.rotation_ = e.rotation),
                (this.rotateWithView_ = e.rotateWithView),
                (this.scale_ = e.scale),
                (this.scaleArray_ = kr(void 0 !== e.scale ? e.scale : 1)),
                (this.text_ = e.text),
                (this.textAlign_ = e.textAlign),
                (this.textBaseline_ = e.textBaseline),
                (this.fill_ =
                  void 0 !== e.fill ? e.fill : new go({ color: "#333" })),
                (this.maxAngle_ =
                  void 0 !== e.maxAngle ? e.maxAngle : Math.PI / 4),
                (this.placement_ =
                  void 0 !== e.placement ? e.placement : "point"),
                (this.overflow_ = !!e.overflow),
                (this.stroke_ = void 0 !== e.stroke ? e.stroke : null),
                (this.offsetX_ = void 0 !== e.offsetX ? e.offsetX : 0),
                (this.offsetY_ = void 0 !== e.offsetY ? e.offsetY : 0),
                (this.backgroundFill_ = e.backgroundFill
                  ? e.backgroundFill
                  : null),
                (this.backgroundStroke_ = e.backgroundStroke
                  ? e.backgroundStroke
                  : null),
                (this.padding_ = void 0 === e.padding ? null : e.padding);
            }
            return (
              (t.prototype.clone = function () {
                var e = this.getScale();
                return new t({
                  font: this.getFont(),
                  placement: this.getPlacement(),
                  maxAngle: this.getMaxAngle(),
                  overflow: this.getOverflow(),
                  rotation: this.getRotation(),
                  rotateWithView: this.getRotateWithView(),
                  scale: Array.isArray(e) ? e.slice() : e,
                  text: this.getText(),
                  textAlign: this.getTextAlign(),
                  textBaseline: this.getTextBaseline(),
                  fill: this.getFill() ? this.getFill().clone() : void 0,
                  stroke: this.getStroke() ? this.getStroke().clone() : void 0,
                  offsetX: this.getOffsetX(),
                  offsetY: this.getOffsetY(),
                  backgroundFill: this.getBackgroundFill()
                    ? this.getBackgroundFill().clone()
                    : void 0,
                  backgroundStroke: this.getBackgroundStroke()
                    ? this.getBackgroundStroke().clone()
                    : void 0,
                  padding: this.getPadding() || void 0,
                });
              }),
              (t.prototype.getOverflow = function () {
                return this.overflow_;
              }),
              (t.prototype.getFont = function () {
                return this.font_;
              }),
              (t.prototype.getMaxAngle = function () {
                return this.maxAngle_;
              }),
              (t.prototype.getPlacement = function () {
                return this.placement_;
              }),
              (t.prototype.getOffsetX = function () {
                return this.offsetX_;
              }),
              (t.prototype.getOffsetY = function () {
                return this.offsetY_;
              }),
              (t.prototype.getFill = function () {
                return this.fill_;
              }),
              (t.prototype.getRotateWithView = function () {
                return this.rotateWithView_;
              }),
              (t.prototype.getRotation = function () {
                return this.rotation_;
              }),
              (t.prototype.getScale = function () {
                return this.scale_;
              }),
              (t.prototype.getScaleArray = function () {
                return this.scaleArray_;
              }),
              (t.prototype.getStroke = function () {
                return this.stroke_;
              }),
              (t.prototype.getText = function () {
                return this.text_;
              }),
              (t.prototype.getTextAlign = function () {
                return this.textAlign_;
              }),
              (t.prototype.getTextBaseline = function () {
                return this.textBaseline_;
              }),
              (t.prototype.getBackgroundFill = function () {
                return this.backgroundFill_;
              }),
              (t.prototype.getBackgroundStroke = function () {
                return this.backgroundStroke_;
              }),
              (t.prototype.getPadding = function () {
                return this.padding_;
              }),
              (t.prototype.setOverflow = function (t) {
                this.overflow_ = t;
              }),
              (t.prototype.setFont = function (t) {
                this.font_ = t;
              }),
              (t.prototype.setMaxAngle = function (t) {
                this.maxAngle_ = t;
              }),
              (t.prototype.setOffsetX = function (t) {
                this.offsetX_ = t;
              }),
              (t.prototype.setOffsetY = function (t) {
                this.offsetY_ = t;
              }),
              (t.prototype.setPlacement = function (t) {
                this.placement_ = t;
              }),
              (t.prototype.setRotateWithView = function (t) {
                this.rotateWithView_ = t;
              }),
              (t.prototype.setFill = function (t) {
                this.fill_ = t;
              }),
              (t.prototype.setRotation = function (t) {
                this.rotation_ = t;
              }),
              (t.prototype.setScale = function (t) {
                (this.scale_ = t),
                  (this.scaleArray_ = kr(void 0 !== t ? t : 1));
              }),
              (t.prototype.setStroke = function (t) {
                this.stroke_ = t;
              }),
              (t.prototype.setText = function (t) {
                this.text_ = t;
              }),
              (t.prototype.setTextAlign = function (t) {
                this.textAlign_ = t;
              }),
              (t.prototype.setTextBaseline = function (t) {
                this.textBaseline_ = t;
              }),
              (t.prototype.setBackgroundFill = function (t) {
                this.backgroundFill_ = t;
              }),
              (t.prototype.setBackgroundStroke = function (t) {
                this.backgroundStroke_ = t;
              }),
              (t.prototype.setPadding = function (t) {
                this.padding_ = t;
              }),
              t
            );
          })(),
          Da = (function () {
            var t = function (e, n) {
              return (
                (t =
                  Object.setPrototypeOf ||
                  ({ __proto__: [] } instanceof Array &&
                    function (t, e) {
                      t.__proto__ = e;
                    }) ||
                  function (t, e) {
                    for (var n in e)
                      Object.prototype.hasOwnProperty.call(e, n) &&
                        (t[n] = e[n]);
                  }),
                t(e, n)
              );
            };
            return function (e, n) {
              if ("function" != typeof n && null !== n)
                throw new TypeError(
                  "Class extends value " +
                    String(n) +
                    " is not a constructor or null"
                );
              function i() {
                this.constructor = e;
              }
              t(e, n),
                (e.prototype =
                  null === n
                    ? Object.create(n)
                    : ((i.prototype = n.prototype), new i()));
            };
          })(),
          ka = (function (t) {
            function e(e) {
              var n = t.call(this) || this;
              if (
                (n.on,
                n.once,
                n.un,
                (n.id_ = void 0),
                (n.geometryName_ = "geometry"),
                (n.style_ = null),
                (n.styleFunction_ = void 0),
                (n.geometryChangeKey_ = null),
                n.addChangeListener(n.geometryName_, n.handleGeometryChanged_),
                e)
              )
                if ("function" == typeof e.getSimplifiedGeometry) {
                  var i = e;
                  n.setGeometry(i);
                } else {
                  var r = e;
                  n.setProperties(r);
                }
              return n;
            }
            return (
              Da(e, t),
              (e.prototype.clone = function () {
                var t = new e(
                  this.hasProperties() ? this.getProperties() : null
                );
                t.setGeometryName(this.getGeometryName());
                var n = this.getGeometry();
                n && t.setGeometry(n.clone());
                var i = this.getStyle();
                return i && t.setStyle(i), t;
              }),
              (e.prototype.getGeometry = function () {
                return this.get(this.geometryName_);
              }),
              (e.prototype.getId = function () {
                return this.id_;
              }),
              (e.prototype.getGeometryName = function () {
                return this.geometryName_;
              }),
              (e.prototype.getStyle = function () {
                return this.style_;
              }),
              (e.prototype.getStyleFunction = function () {
                return this.styleFunction_;
              }),
              (e.prototype.handleGeometryChange_ = function () {
                this.changed();
              }),
              (e.prototype.handleGeometryChanged_ = function () {
                this.geometryChangeKey_ &&
                  (I(this.geometryChangeKey_),
                  (this.geometryChangeKey_ = null));
                var t = this.getGeometry();
                t &&
                  (this.geometryChangeKey_ = O(
                    t,
                    x,
                    this.handleGeometryChange_,
                    this
                  )),
                  this.changed();
              }),
              (e.prototype.setGeometry = function (t) {
                this.set(this.geometryName_, t);
              }),
              (e.prototype.setStyle = function (t) {
                var e, n;
                (this.style_ = t),
                  (this.styleFunction_ = t
                    ? "function" == typeof (e = t)
                      ? e
                      : (Array.isArray(e)
                          ? (n = e)
                          : (vt("function" == typeof e.getZIndex, 41),
                            (n = [e])),
                        function () {
                          return n;
                        })
                    : void 0),
                  this.changed();
              }),
              (e.prototype.setId = function (t) {
                (this.id_ = t), this.changed();
              }),
              (e.prototype.setGeometryName = function (t) {
                this.removeChangeListener(
                  this.geometryName_,
                  this.handleGeometryChanged_
                ),
                  (this.geometryName_ = t),
                  this.addChangeListener(
                    this.geometryName_,
                    this.handleGeometryChanged_
                  ),
                  this.handleGeometryChanged_();
              }),
              e
            );
          })(G),
          ja = ka,
          Ga = (function () {
            var t = function (e, n) {
              return (
                (t =
                  Object.setPrototypeOf ||
                  ({ __proto__: [] } instanceof Array &&
                    function (t, e) {
                      t.__proto__ = e;
                    }) ||
                  function (t, e) {
                    for (var n in e)
                      Object.prototype.hasOwnProperty.call(e, n) &&
                        (t[n] = e[n]);
                  }),
                t(e, n)
              );
            };
            return function (e, n) {
              if ("function" != typeof n && null !== n)
                throw new TypeError(
                  "Class extends value " +
                    String(n) +
                    " is not a constructor or null"
                );
              function i() {
                this.constructor = e;
              }
              t(e, n),
                (e.prototype =
                  null === n
                    ? Object.create(n)
                    : ((i.prototype = n.prototype), new i()));
            };
          })(),
          za = (function (t) {
            function e(e) {
              var n = t.call(this) || this;
              return (n.map_ = e), n;
            }
            return (
              Ga(e, t),
              (e.prototype.dispatchRenderEvent = function (t, e) {
                L();
              }),
              (e.prototype.calculateMatrices2D = function (t) {
                var e = t.viewState,
                  n = t.coordinateToPixelTransform,
                  i = t.pixelToCoordinateTransform;
                Gn(
                  n,
                  t.size[0] / 2,
                  t.size[1] / 2,
                  1 / e.resolution,
                  -1 / e.resolution,
                  -e.rotation,
                  -e.center[0],
                  -e.center[1]
                ),
                  zn(i, n);
              }),
              (e.prototype.forEachFeatureAtCoordinate = function (
                t,
                e,
                n,
                i,
                r,
                o,
                s,
                a
              ) {
                var l,
                  h = e.viewState;
                function u(t, e, n, i) {
                  return r.call(o, e, t ? n : null, i);
                }
                var c = h.projection,
                  p = Xe(t.slice(), c),
                  f = [[0, 0]];
                if (c.canWrapX() && i) {
                  var d = ke(c.getExtent());
                  f.push([-d, 0], [d, 0]);
                }
                for (
                  var g = e.layerStatesArray,
                    _ = g.length,
                    y = [],
                    v = [],
                    m = 0;
                  m < f.length;
                  m++
                )
                  for (var x = _ - 1; x >= 0; --x) {
                    var C = g[x],
                      w = C.layer;
                    if (w.hasRenderer() && jt(C, h) && s.call(a, w)) {
                      var S = w.getRenderer(),
                        E = w.getSource();
                      if (S && E) {
                        var T = E.getWrapX() ? p : t,
                          b = u.bind(null, C.managed);
                        (v[0] = T[0] + f[m][0]),
                          (v[1] = T[1] + f[m][1]),
                          (l = S.forEachFeatureAtCoordinate(v, e, n, b, y));
                      }
                      if (l) return l;
                    }
                  }
                if (0 !== y.length) {
                  var O = 1 / y.length;
                  return (
                    y.forEach(function (t, e) {
                      return (t.distanceSq += e * O);
                    }),
                    y.sort(function (t, e) {
                      return t.distanceSq - e.distanceSq;
                    }),
                    y.some(function (t) {
                      return (l = t.callback(t.feature, t.layer, t.geometry));
                    }),
                    l
                  );
                }
              }),
              (e.prototype.forEachLayerAtPixel = function (t, e, n, i, r) {
                return L();
              }),
              (e.prototype.hasFeatureAtCoordinate = function (
                t,
                e,
                n,
                i,
                r,
                o
              ) {
                return (
                  void 0 !==
                  this.forEachFeatureAtCoordinate(t, e, n, i, u, this, r, o)
                );
              }),
              (e.prototype.getMap = function () {
                return this.map_;
              }),
              (e.prototype.renderFrame = function (t) {
                L();
              }),
              (e.prototype.scheduleExpireIconCache = function (t) {
                bs.canExpireCache() && t.postRenderFunctions.push(Wa);
              }),
              e
            );
          })(r);
        function Wa(t, e) {
          bs.expire();
        }
        var Xa = za,
          Na = (function () {
            var t = function (e, n) {
              return (
                (t =
                  Object.setPrototypeOf ||
                  ({ __proto__: [] } instanceof Array &&
                    function (t, e) {
                      t.__proto__ = e;
                    }) ||
                  function (t, e) {
                    for (var n in e)
                      Object.prototype.hasOwnProperty.call(e, n) &&
                        (t[n] = e[n]);
                  }),
                t(e, n)
              );
            };
            return function (e, n) {
              if ("function" != typeof n && null !== n)
                throw new TypeError(
                  "Class extends value " +
                    String(n) +
                    " is not a constructor or null"
                );
              function i() {
                this.constructor = e;
              }
              t(e, n),
                (e.prototype =
                  null === n
                    ? Object.create(n)
                    : ((i.prototype = n.prototype), new i()));
            };
          })(),
          Ya = (function (t) {
            function n(n) {
              var i = t.call(this, n) || this;
              (i.fontChangeListenerKey_ = O(to, e, n.redrawText.bind(n))),
                (i.element_ = document.createElement("div"));
              var r = i.element_.style;
              (r.position = "absolute"),
                (r.width = "100%"),
                (r.height = "100%"),
                (r.zIndex = "0"),
                (i.element_.className = "ol-unselectable ol-layers");
              var o = n.getViewport();
              return (
                o.insertBefore(i.element_, o.firstChild || null),
                (i.children_ = []),
                (i.renderedVisible_ = !0),
                i
              );
            }
            return (
              Na(n, t),
              (n.prototype.dispatchRenderEvent = function (t, e) {
                var n = this.getMap();
                if (n.hasListener(t)) {
                  var i = new ir(t, void 0, e);
                  n.dispatchEvent(i);
                }
              }),
              (n.prototype.disposeInternal = function () {
                I(this.fontChangeListenerKey_),
                  this.element_.parentNode.removeChild(this.element_),
                  t.prototype.disposeInternal.call(this);
              }),
              (n.prototype.renderFrame = function (t) {
                if (t) {
                  this.calculateMatrices2D(t), this.dispatchRenderEvent(Ft, t);
                  var e = t.layerStatesArray.sort(function (t, e) {
                      return t.zIndex - e.zIndex;
                    }),
                    n = t.viewState;
                  this.children_.length = 0;
                  for (var i = [], r = null, o = 0, s = e.length; o < s; ++o) {
                    var a = e[o];
                    t.layerIndex = o;
                    var l = a.layer,
                      h = l.getSourceState();
                    if (!jt(a, n) || (h != Dt && h != At)) l.unrender();
                    else {
                      var u = l.render(t, r);
                      u &&
                        (u !== r && (this.children_.push(u), (r = u)),
                        "getDeclutter" in l && i.push(l));
                    }
                  }
                  for (o = i.length - 1; o >= 0; --o) i[o].renderDeclutter(t);
                  !(function (t, e) {
                    for (var n = t.childNodes, i = 0; ; ++i) {
                      var r = n[i],
                        o = e[i];
                      if (!r && !o) break;
                      r !== o &&
                        (r
                          ? o
                            ? t.insertBefore(o, r)
                            : (t.removeChild(r), --i)
                          : t.appendChild(o));
                    }
                  })(this.element_, this.children_),
                    this.dispatchRenderEvent("postcompose", t),
                    this.renderedVisible_ ||
                      ((this.element_.style.display = ""),
                      (this.renderedVisible_ = !0)),
                    this.scheduleExpireIconCache(t);
                } else
                  this.renderedVisible_ &&
                    ((this.element_.style.display = "none"),
                    (this.renderedVisible_ = !1));
              }),
              (n.prototype.forEachLayerAtPixel = function (t, e, n, i, r) {
                for (
                  var o = e.viewState, s = e.layerStatesArray, a = s.length - 1;
                  a >= 0;
                  --a
                ) {
                  var l = s[a],
                    h = l.layer;
                  if (h.hasRenderer() && jt(l, o) && r(h)) {
                    var u = h.getRenderer().getDataAtPixel(t, e, n);
                    if (u) {
                      var c = i(h, u);
                      if (c) return c;
                    }
                  }
                }
              }),
              n
            );
          })(Xa),
          Ba = (function () {
            var t = function (e, n) {
              return (
                (t =
                  Object.setPrototypeOf ||
                  ({ __proto__: [] } instanceof Array &&
                    function (t, e) {
                      t.__proto__ = e;
                    }) ||
                  function (t, e) {
                    for (var n in e)
                      Object.prototype.hasOwnProperty.call(e, n) &&
                        (t[n] = e[n]);
                  }),
                t(e, n)
              );
            };
            return function (e, n) {
              if ("function" != typeof n && null !== n)
                throw new TypeError(
                  "Class extends value " +
                    String(n) +
                    " is not a constructor or null"
                );
              function i() {
                this.constructor = e;
              }
              t(e, n),
                (e.prototype =
                  null === n
                    ? Object.create(n)
                    : ((i.prototype = n.prototype), new i()));
            };
          })(),
          Ka = (function (t) {
            function e(e, n) {
              var i = t.call(this, e) || this;
              return (i.layer = n), i;
            }
            return Ba(e, t), e;
          })(t),
          Za = "layers",
          Va = (function (t) {
            function n(e) {
              var n = this,
                i = e || {},
                r = f({}, i);
              delete r.layers;
              var o = i.layers;
              return (
                (n = t.call(this, r) || this).on,
                n.once,
                n.un,
                (n.layersListenerKeys_ = []),
                (n.listenerKeys_ = {}),
                n.addChangeListener(Za, n.handleLayersChanged_),
                o
                  ? Array.isArray(o)
                    ? (o = new Ea(o.slice(), { unique: !0 }))
                    : vt("function" == typeof o.getArray, 43)
                  : (o = new Ea(void 0, { unique: !0 })),
                n.setLayers(o),
                n
              );
            }
            return (
              Ba(n, t),
              (n.prototype.handleLayerChange_ = function () {
                this.changed();
              }),
              (n.prototype.handleLayersChanged_ = function () {
                this.layersListenerKeys_.forEach(I),
                  (this.layersListenerKeys_.length = 0);
                var t = this.getLayers();
                for (var e in (this.layersListenerKeys_.push(
                  O(t, ma, this.handleLayersAdd_, this),
                  O(t, xa, this.handleLayersRemove_, this)
                ),
                this.listenerKeys_))
                  this.listenerKeys_[e].forEach(I);
                d(this.listenerKeys_);
                for (var n = t.getArray(), i = 0, r = n.length; i < r; i++) {
                  var o = n[i];
                  this.registerLayerListeners_(o),
                    this.dispatchEvent(new Ka("addlayer", o));
                }
                this.changed();
              }),
              (n.prototype.registerLayerListeners_ = function (t) {
                var i = [
                  O(t, e, this.handleLayerChange_, this),
                  O(t, x, this.handleLayerChange_, this),
                ];
                t instanceof n &&
                  i.push(
                    O(t, "addlayer", this.handleLayerGroupAdd_, this),
                    O(t, "removelayer", this.handleLayerGroupRemove_, this)
                  ),
                  (this.listenerKeys_[D(t)] = i);
              }),
              (n.prototype.handleLayerGroupAdd_ = function (t) {
                this.dispatchEvent(new Ka("addlayer", t.layer));
              }),
              (n.prototype.handleLayerGroupRemove_ = function (t) {
                this.dispatchEvent(new Ka("removelayer", t.layer));
              }),
              (n.prototype.handleLayersAdd_ = function (t) {
                var e = t.element;
                this.registerLayerListeners_(e),
                  this.dispatchEvent(new Ka("addlayer", e)),
                  this.changed();
              }),
              (n.prototype.handleLayersRemove_ = function (t) {
                var e = t.element,
                  n = D(e);
                this.listenerKeys_[n].forEach(I),
                  delete this.listenerKeys_[n],
                  this.dispatchEvent(new Ka("removelayer", e)),
                  this.changed();
              }),
              (n.prototype.getLayers = function () {
                return this.get(Za);
              }),
              (n.prototype.setLayers = function (t) {
                var e = this.getLayers();
                if (e)
                  for (var n = e.getArray(), i = 0, r = n.length; i < r; ++i)
                    this.dispatchEvent(new Ka("removelayer", n[i]));
                this.set(Za, t);
              }),
              (n.prototype.getLayersArray = function (t) {
                var e = void 0 !== t ? t : [];
                return (
                  this.getLayers().forEach(function (t) {
                    t.getLayersArray(e);
                  }),
                  e
                );
              }),
              (n.prototype.getLayerStatesArray = function (t) {
                var e = void 0 !== t ? t : [],
                  n = e.length;
                this.getLayers().forEach(function (t) {
                  t.getLayerStatesArray(e);
                });
                var i = this.getLayerState(),
                  r = i.zIndex;
                t || void 0 !== i.zIndex || (r = 0);
                for (var o = n, s = e.length; o < s; o++) {
                  var a = e[o];
                  (a.opacity *= i.opacity),
                    (a.visible = a.visible && i.visible),
                    (a.maxResolution = Math.min(
                      a.maxResolution,
                      i.maxResolution
                    )),
                    (a.minResolution = Math.max(
                      a.minResolution,
                      i.minResolution
                    )),
                    (a.minZoom = Math.max(a.minZoom, i.minZoom)),
                    (a.maxZoom = Math.min(a.maxZoom, i.maxZoom)),
                    void 0 !== i.extent &&
                      (void 0 !== a.extent
                        ? (a.extent = Le(a.extent, i.extent))
                        : (a.extent = i.extent)),
                    void 0 === a.zIndex && (a.zIndex = r);
                }
                return e;
              }),
              (n.prototype.getSourceState = function () {
                return Dt;
              }),
              n
            );
          })(Mt),
          Ua = (function () {
            var t = function (e, n) {
              return (
                (t =
                  Object.setPrototypeOf ||
                  ({ __proto__: [] } instanceof Array &&
                    function (t, e) {
                      t.__proto__ = e;
                    }) ||
                  function (t, e) {
                    for (var n in e)
                      Object.prototype.hasOwnProperty.call(e, n) &&
                        (t[n] = e[n]);
                  }),
                t(e, n)
              );
            };
            return function (e, n) {
              if ("function" != typeof n && null !== n)
                throw new TypeError(
                  "Class extends value " +
                    String(n) +
                    " is not a constructor or null"
                );
              function i() {
                this.constructor = e;
              }
              t(e, n),
                (e.prototype =
                  null === n
                    ? Object.create(n)
                    : ((i.prototype = n.prototype), new i()));
            };
          })(),
          Ha = (function (t) {
            function e(e, n, i) {
              var r = t.call(this, e) || this;
              return (r.map = n), (r.frameState = void 0 !== i ? i : null), r;
            }
            return Ua(e, t), e;
          })(t),
          qa = (function () {
            var t = function (e, n) {
              return (
                (t =
                  Object.setPrototypeOf ||
                  ({ __proto__: [] } instanceof Array &&
                    function (t, e) {
                      t.__proto__ = e;
                    }) ||
                  function (t, e) {
                    for (var n in e)
                      Object.prototype.hasOwnProperty.call(e, n) &&
                        (t[n] = e[n]);
                  }),
                t(e, n)
              );
            };
            return function (e, n) {
              if ("function" != typeof n && null !== n)
                throw new TypeError(
                  "Class extends value " +
                    String(n) +
                    " is not a constructor or null"
                );
              function i() {
                this.constructor = e;
              }
              t(e, n),
                (e.prototype =
                  null === n
                    ? Object.create(n)
                    : ((i.prototype = n.prototype), new i()));
            };
          })(),
          Ja = (function (t) {
            function e(e, n, i, r, o) {
              var s = t.call(this, e, n, o) || this;
              return (
                (s.originalEvent = i),
                (s.pixel_ = null),
                (s.coordinate_ = null),
                (s.dragging = void 0 !== r && r),
                s
              );
            }
            return (
              qa(e, t),
              Object.defineProperty(e.prototype, "pixel", {
                get: function () {
                  return (
                    this.pixel_ ||
                      (this.pixel_ = this.map.getEventPixel(
                        this.originalEvent
                      )),
                    this.pixel_
                  );
                },
                set: function (t) {
                  this.pixel_ = t;
                },
                enumerable: !1,
                configurable: !0,
              }),
              Object.defineProperty(e.prototype, "coordinate", {
                get: function () {
                  return (
                    this.coordinate_ ||
                      (this.coordinate_ = this.map.getCoordinateFromPixel(
                        this.pixel
                      )),
                    this.coordinate_
                  );
                },
                set: function (t) {
                  this.coordinate_ = t;
                },
                enumerable: !1,
                configurable: !0,
              }),
              (e.prototype.preventDefault = function () {
                t.prototype.preventDefault.call(this),
                  "preventDefault" in this.originalEvent &&
                    this.originalEvent.preventDefault();
              }),
              (e.prototype.stopPropagation = function () {
                t.prototype.stopPropagation.call(this),
                  "stopPropagation" in this.originalEvent &&
                    this.originalEvent.stopPropagation();
              }),
              e
            );
          })(Ha),
          Qa = {
            SINGLECLICK: "singleclick",
            CLICK: w,
            DBLCLICK: "dblclick",
            POINTERDRAG: "pointerdrag",
            POINTERMOVE: "pointermove",
            POINTERDOWN: "pointerdown",
            POINTERUP: "pointerup",
            POINTEROVER: "pointerover",
            POINTEROUT: "pointerout",
            POINTERENTER: "pointerenter",
            POINTERLEAVE: "pointerleave",
            POINTERCANCEL: "pointercancel",
          },
          $a = (function () {
            var t = function (e, n) {
              return (
                (t =
                  Object.setPrototypeOf ||
                  ({ __proto__: [] } instanceof Array &&
                    function (t, e) {
                      t.__proto__ = e;
                    }) ||
                  function (t, e) {
                    for (var n in e)
                      Object.prototype.hasOwnProperty.call(e, n) &&
                        (t[n] = e[n]);
                  }),
                t(e, n)
              );
            };
            return function (e, n) {
              if ("function" != typeof n && null !== n)
                throw new TypeError(
                  "Class extends value " +
                    String(n) +
                    " is not a constructor or null"
                );
              function i() {
                this.constructor = e;
              }
              t(e, n),
                (e.prototype =
                  null === n
                    ? Object.create(n)
                    : ((i.prototype = n.prototype), new i()));
            };
          })(),
          tl = (function (t) {
            function e(e, n) {
              var i = t.call(this, e) || this;
              (i.map_ = e),
                i.clickTimeoutId_,
                (i.emulateClicks_ = !1),
                (i.dragging_ = !1),
                (i.dragListenerKeys_ = []),
                (i.moveTolerance_ = void 0 === n ? 1 : n),
                (i.down_ = null);
              var r = i.map_.getViewport();
              return (
                (i.activePointers_ = 0),
                (i.trackedTouches_ = {}),
                (i.element_ = r),
                (i.pointerdownListenerKey_ = O(r, Nt, i.handlePointerDown_, i)),
                i.originalPointerMoveEvent_,
                (i.relayedListenerKey_ = O(r, Xt, i.relayEvent_, i)),
                (i.boundHandleTouchMove_ = i.handleTouchMove_.bind(i)),
                i.element_.addEventListener(
                  T,
                  i.boundHandleTouchMove_,
                  !!H && { passive: !1 }
                ),
                i
              );
            }
            return (
              $a(e, t),
              (e.prototype.emulateClick_ = function (t) {
                var e = new Ja(Qa.CLICK, this.map_, t);
                this.dispatchEvent(e),
                  void 0 !== this.clickTimeoutId_
                    ? (clearTimeout(this.clickTimeoutId_),
                      (this.clickTimeoutId_ = void 0),
                      (e = new Ja(Qa.DBLCLICK, this.map_, t)),
                      this.dispatchEvent(e))
                    : (this.clickTimeoutId_ = setTimeout(
                        function () {
                          this.clickTimeoutId_ = void 0;
                          var e = new Ja(Qa.SINGLECLICK, this.map_, t);
                          this.dispatchEvent(e);
                        }.bind(this),
                        250
                      ));
              }),
              (e.prototype.updateActivePointers_ = function (t) {
                var e = t;
                e.type == Qa.POINTERUP || e.type == Qa.POINTERCANCEL
                  ? delete this.trackedTouches_[e.pointerId]
                  : e.type == Qa.POINTERDOWN &&
                    (this.trackedTouches_[e.pointerId] = !0),
                  (this.activePointers_ = Object.keys(
                    this.trackedTouches_
                  ).length);
              }),
              (e.prototype.handlePointerUp_ = function (t) {
                this.updateActivePointers_(t);
                var e = new Ja(Qa.POINTERUP, this.map_, t);
                this.dispatchEvent(e),
                  this.emulateClicks_ &&
                    !e.defaultPrevented &&
                    !this.dragging_ &&
                    this.isMouseActionButton_(t) &&
                    this.emulateClick_(this.down_),
                  0 === this.activePointers_ &&
                    (this.dragListenerKeys_.forEach(I),
                    (this.dragListenerKeys_.length = 0),
                    (this.dragging_ = !1),
                    (this.down_ = null));
              }),
              (e.prototype.isMouseActionButton_ = function (t) {
                return 0 === t.button;
              }),
              (e.prototype.handlePointerDown_ = function (t) {
                (this.emulateClicks_ = 0 === this.activePointers_),
                  this.updateActivePointers_(t);
                var e = new Ja(Qa.POINTERDOWN, this.map_, t);
                for (var n in (this.dispatchEvent(e), (this.down_ = {}), t)) {
                  var i = t[n];
                  this.down_[n] = "function" == typeof i ? p : i;
                }
                if (0 === this.dragListenerKeys_.length) {
                  var r = this.map_.getOwnerDocument();
                  this.dragListenerKeys_.push(
                    O(r, Qa.POINTERMOVE, this.handlePointerMove_, this),
                    O(r, Qa.POINTERUP, this.handlePointerUp_, this),
                    O(
                      this.element_,
                      Qa.POINTERCANCEL,
                      this.handlePointerUp_,
                      this
                    )
                  ),
                    this.element_.getRootNode &&
                      this.element_.getRootNode() !== r &&
                      this.dragListenerKeys_.push(
                        O(
                          this.element_.getRootNode(),
                          Qa.POINTERUP,
                          this.handlePointerUp_,
                          this
                        )
                      );
                }
              }),
              (e.prototype.handlePointerMove_ = function (t) {
                if (this.isMoving_(t)) {
                  this.dragging_ = !0;
                  var e = new Ja(Qa.POINTERDRAG, this.map_, t, this.dragging_);
                  this.dispatchEvent(e);
                }
              }),
              (e.prototype.relayEvent_ = function (t) {
                this.originalPointerMoveEvent_ = t;
                var e = !(!this.down_ || !this.isMoving_(t));
                this.dispatchEvent(new Ja(t.type, this.map_, t, e));
              }),
              (e.prototype.handleTouchMove_ = function (t) {
                var e = this.originalPointerMoveEvent_;
                (e && !e.defaultPrevented) ||
                  ("boolean" == typeof t.cancelable && !0 !== t.cancelable) ||
                  t.preventDefault();
              }),
              (e.prototype.isMoving_ = function (t) {
                return (
                  this.dragging_ ||
                  Math.abs(t.clientX - this.down_.clientX) >
                    this.moveTolerance_ ||
                  Math.abs(t.clientY - this.down_.clientY) > this.moveTolerance_
                );
              }),
              (e.prototype.disposeInternal = function () {
                this.relayedListenerKey_ &&
                  (I(this.relayedListenerKey_),
                  (this.relayedListenerKey_ = null)),
                  this.element_.removeEventListener(
                    T,
                    this.boundHandleTouchMove_
                  ),
                  this.pointerdownListenerKey_ &&
                    (I(this.pointerdownListenerKey_),
                    (this.pointerdownListenerKey_ = null)),
                  this.dragListenerKeys_.forEach(I),
                  (this.dragListenerKeys_.length = 0),
                  (this.element_ = null),
                  t.prototype.disposeInternal.call(this);
              }),
              e
            );
          })(m),
          el = "layergroup",
          nl = "size",
          il = "target",
          rl = "view",
          ol = 1 / 0,
          sl = (function () {
            function t(t, e) {
              (this.priorityFunction_ = t),
                (this.keyFunction_ = e),
                (this.elements_ = []),
                (this.priorities_ = []),
                (this.queuedElements_ = {});
            }
            return (
              (t.prototype.clear = function () {
                (this.elements_.length = 0),
                  (this.priorities_.length = 0),
                  d(this.queuedElements_);
              }),
              (t.prototype.dequeue = function () {
                var t = this.elements_,
                  e = this.priorities_,
                  n = t[0];
                1 == t.length
                  ? ((t.length = 0), (e.length = 0))
                  : ((t[0] = t.pop()), (e[0] = e.pop()), this.siftUp_(0));
                var i = this.keyFunction_(n);
                return delete this.queuedElements_[i], n;
              }),
              (t.prototype.enqueue = function (t) {
                vt(!(this.keyFunction_(t) in this.queuedElements_), 31);
                var e = this.priorityFunction_(t);
                return (
                  e != ol &&
                  (this.elements_.push(t),
                  this.priorities_.push(e),
                  (this.queuedElements_[this.keyFunction_(t)] = !0),
                  this.siftDown_(0, this.elements_.length - 1),
                  !0)
                );
              }),
              (t.prototype.getCount = function () {
                return this.elements_.length;
              }),
              (t.prototype.getLeftChildIndex_ = function (t) {
                return 2 * t + 1;
              }),
              (t.prototype.getRightChildIndex_ = function (t) {
                return 2 * t + 2;
              }),
              (t.prototype.getParentIndex_ = function (t) {
                return (t - 1) >> 1;
              }),
              (t.prototype.heapify_ = function () {
                var t;
                for (t = (this.elements_.length >> 1) - 1; t >= 0; t--)
                  this.siftUp_(t);
              }),
              (t.prototype.isEmpty = function () {
                return 0 === this.elements_.length;
              }),
              (t.prototype.isKeyQueued = function (t) {
                return t in this.queuedElements_;
              }),
              (t.prototype.isQueued = function (t) {
                return this.isKeyQueued(this.keyFunction_(t));
              }),
              (t.prototype.siftUp_ = function (t) {
                for (
                  var e = this.elements_,
                    n = this.priorities_,
                    i = e.length,
                    r = e[t],
                    o = n[t],
                    s = t;
                  t < i >> 1;

                ) {
                  var a = this.getLeftChildIndex_(t),
                    l = this.getRightChildIndex_(t),
                    h = l < i && n[l] < n[a] ? l : a;
                  (e[t] = e[h]), (n[t] = n[h]), (t = h);
                }
                (e[t] = r), (n[t] = o), this.siftDown_(s, t);
              }),
              (t.prototype.siftDown_ = function (t, e) {
                for (
                  var n = this.elements_,
                    i = this.priorities_,
                    r = n[e],
                    o = i[e];
                  e > t;

                ) {
                  var s = this.getParentIndex_(e);
                  if (!(i[s] > o)) break;
                  (n[e] = n[s]), (i[e] = i[s]), (e = s);
                }
                (n[e] = r), (i[e] = o);
              }),
              (t.prototype.reprioritize = function () {
                var t,
                  e,
                  n,
                  i = this.priorityFunction_,
                  r = this.elements_,
                  o = this.priorities_,
                  s = 0,
                  a = r.length;
                for (e = 0; e < a; ++e)
                  (n = i((t = r[e]))) == ol
                    ? delete this.queuedElements_[this.keyFunction_(t)]
                    : ((o[s] = n), (r[s++] = t));
                (r.length = s), (o.length = s), this.heapify_();
              }),
              t
            );
          })(),
          al = (function () {
            var t = function (e, n) {
              return (
                (t =
                  Object.setPrototypeOf ||
                  ({ __proto__: [] } instanceof Array &&
                    function (t, e) {
                      t.__proto__ = e;
                    }) ||
                  function (t, e) {
                    for (var n in e)
                      Object.prototype.hasOwnProperty.call(e, n) &&
                        (t[n] = e[n]);
                  }),
                t(e, n)
              );
            };
            return function (e, n) {
              if ("function" != typeof n && null !== n)
                throw new TypeError(
                  "Class extends value " +
                    String(n) +
                    " is not a constructor or null"
                );
              function i() {
                this.constructor = e;
              }
              t(e, n),
                (e.prototype =
                  null === n
                    ? Object.create(n)
                    : ((i.prototype = n.prototype), new i()));
            };
          })(),
          ll = (function (t) {
            function e(e, n) {
              var i =
                t.call(
                  this,
                  function (t) {
                    return e.apply(null, t);
                  },
                  function (t) {
                    return t[0].getKey();
                  }
                ) || this;
              return (
                (i.boundHandleTileChange_ = i.handleTileChange.bind(i)),
                (i.tileChangeCallback_ = n),
                (i.tilesLoading_ = 0),
                (i.tilesLoadingKeys_ = {}),
                i
              );
            }
            return (
              al(e, t),
              (e.prototype.enqueue = function (e) {
                var n = t.prototype.enqueue.call(this, e);
                return (
                  n && e[0].addEventListener(x, this.boundHandleTileChange_), n
                );
              }),
              (e.prototype.getTilesLoading = function () {
                return this.tilesLoading_;
              }),
              (e.prototype.handleTileChange = function (t) {
                var e = t.target,
                  n = e.getState();
                if (2 === n || 3 === n || 4 === n) {
                  e.removeEventListener(x, this.boundHandleTileChange_);
                  var i = e.getKey();
                  i in this.tilesLoadingKeys_ &&
                    (delete this.tilesLoadingKeys_[i], --this.tilesLoading_),
                    this.tileChangeCallback_();
                }
              }),
              (e.prototype.loadMoreTiles = function (t, e) {
                for (
                  var n, i, r = 0;
                  this.tilesLoading_ < t && r < e && this.getCount() > 0;

                )
                  (i = (n = this.dequeue()[0]).getKey()),
                    0 !== n.getState() ||
                      i in this.tilesLoadingKeys_ ||
                      ((this.tilesLoadingKeys_[i] = !0),
                      ++this.tilesLoading_,
                      ++r,
                      n.load());
              }),
              e
            );
          })(sl),
          hl = {
            CENTER: "center",
            RESOLUTION: "resolution",
            ROTATION: "rotation",
          };
        function ul(t, e, n) {
          return function (i, r, o, s, a) {
            if (i) {
              if (!r && !e) return i;
              var l = e ? 0 : o[0] * r,
                h = e ? 0 : o[1] * r,
                u = a ? a[0] : 0,
                c = a ? a[1] : 0,
                p = t[0] + l / 2 + u,
                f = t[2] - l / 2 + u,
                d = t[1] + h / 2 + c,
                g = t[3] - h / 2 + c;
              p > f && (f = p = (f + p) / 2), d > g && (g = d = (g + d) / 2);
              var _ = mt(i[0], p, f),
                y = mt(i[1], d, g);
              if (s && n && r) {
                var v = 30 * r;
                (_ +=
                  -v * Math.log(1 + Math.max(0, p - i[0]) / v) +
                  v * Math.log(1 + Math.max(0, i[0] - f) / v)),
                  (y +=
                    -v * Math.log(1 + Math.max(0, d - i[1]) / v) +
                    v * Math.log(1 + Math.max(0, i[1] - g) / v));
              }
              return [_, y];
            }
          };
        }
        function cl(t) {
          return t;
        }
        function pl(t, e, n, i) {
          var r = ke(e) / n[0],
            o = Fe(e) / n[1];
          return i ? Math.min(t, Math.max(r, o)) : Math.min(t, Math.min(r, o));
        }
        function fl(t, e, n) {
          var i = Math.min(t, e);
          return (
            (i *= Math.log(1 + 50 * Math.max(0, t / e - 1)) / 50 + 1),
            n &&
              ((i = Math.max(i, n)),
              (i /= Math.log(1 + 50 * Math.max(0, n / t - 1)) / 50 + 1)),
            mt(i, n / 2, 2 * e)
          );
        }
        function dl(t, e, n, i, r) {
          return function (o, s, a, l) {
            if (void 0 !== o) {
              var h = i ? pl(t, i, a, r) : t;
              return (void 0 === n || n) && l ? fl(o, h, e) : mt(o, e, h);
            }
          };
        }
        function gl(t) {
          return void 0 !== t ? 0 : void 0;
        }
        function _l(t) {
          return void 0 !== t ? t : void 0;
        }
        var yl = (function () {
            var t = function (e, n) {
              return (
                (t =
                  Object.setPrototypeOf ||
                  ({ __proto__: [] } instanceof Array &&
                    function (t, e) {
                      t.__proto__ = e;
                    }) ||
                  function (t, e) {
                    for (var n in e)
                      Object.prototype.hasOwnProperty.call(e, n) &&
                        (t[n] = e[n]);
                  }),
                t(e, n)
              );
            };
            return function (e, n) {
              if ("function" != typeof n && null !== n)
                throw new TypeError(
                  "Class extends value " +
                    String(n) +
                    " is not a constructor or null"
                );
              function i() {
                this.constructor = e;
              }
              t(e, n),
                (e.prototype =
                  null === n
                    ? Object.create(n)
                    : ((i.prototype = n.prototype), new i()));
            };
          })(),
          vl = (function (t) {
            function e(e) {
              var n = t.call(this) || this;
              n.on, n.once, n.un;
              var i = f({}, e);
              return (
                (n.hints_ = [0, 0]),
                (n.animations_ = []),
                n.updateAnimationKey_,
                (n.projection_ = Je(i.projection, "EPSG:3857")),
                (n.viewportSize_ = [100, 100]),
                (n.targetCenter_ = null),
                n.targetResolution_,
                n.targetRotation_,
                (n.nextCenter_ = null),
                n.nextResolution_,
                n.nextRotation_,
                (n.cancelAnchor_ = void 0),
                i.projection && Be(),
                i.center && (i.center = un(i.center, n.projection_)),
                i.extent && (i.extent = pn(i.extent, n.projection_)),
                n.applyOptions_(i),
                n
              );
            }
            return (
              yl(e, t),
              (e.prototype.applyOptions_ = function (t) {
                var e = f({}, t);
                for (var n in hl) delete e[n];
                this.setProperties(e, !0);
                var i = (function (t) {
                  var e,
                    n,
                    i,
                    r = void 0 !== t.minZoom ? t.minZoom : 0,
                    o = void 0 !== t.maxZoom ? t.maxZoom : 28,
                    a = void 0 !== t.zoomFactor ? t.zoomFactor : 2,
                    l = void 0 !== t.multiWorld && t.multiWorld,
                    h =
                      void 0 === t.smoothResolutionConstraint ||
                      t.smoothResolutionConstraint,
                    u = void 0 !== t.showFullExtent && t.showFullExtent,
                    c = Je(t.projection, "EPSG:3857"),
                    p = c.getExtent(),
                    f = t.constrainOnlyCenter,
                    d = t.extent;
                  if (
                    (l || d || !c.isGlobal() || ((f = !1), (d = p)),
                    void 0 !== t.resolutions)
                  ) {
                    var g = t.resolutions;
                    (n = g[r]),
                      (i = void 0 !== g[o] ? g[o] : g[g.length - 1]),
                      (e = t.constrainResolution
                        ? (function (t, e, n, i) {
                            return function (r, o, a, l) {
                              if (void 0 !== r) {
                                var h = t[0],
                                  u = t[t.length - 1],
                                  c = n ? pl(h, n, a, i) : h;
                                if (l)
                                  return void 0 === e || e
                                    ? fl(r, c, u)
                                    : mt(r, u, c);
                                var p = Math.min(c, r),
                                  f = Math.floor(s(t, p, o));
                                return t[f] > c && f < t.length - 1
                                  ? t[f + 1]
                                  : t[f];
                              }
                            };
                          })(g, h, !f && d, u)
                        : dl(n, i, h, !f && d, u));
                  } else {
                    var _ =
                        (p
                          ? Math.max(ke(p), Fe(p))
                          : (360 * Bt[Kt.DEGREES]) / c.getMetersPerUnit()) /
                        256 /
                        Math.pow(2, 0),
                      y = _ / Math.pow(2, 28);
                    void 0 !== (n = t.maxResolution)
                      ? (r = 0)
                      : (n = _ / Math.pow(a, r)),
                      void 0 === (i = t.minResolution) &&
                        (i =
                          void 0 !== t.maxZoom
                            ? void 0 !== t.maxResolution
                              ? n / Math.pow(a, o)
                              : _ / Math.pow(a, o)
                            : y),
                      (o = r + Math.floor(Math.log(n / i) / Math.log(a))),
                      (i = n / Math.pow(a, o - r)),
                      (e = t.constrainResolution
                        ? (function (t, e, n, i, r, o) {
                            return function (s, a, l, h) {
                              if (void 0 !== s) {
                                var u = r ? pl(e, r, l, o) : e,
                                  c = void 0 !== n ? n : 0;
                                if (h)
                                  return void 0 === i || i
                                    ? fl(s, u, c)
                                    : mt(s, c, u);
                                var p = Math.ceil(
                                    Math.log(e / u) / Math.log(t) - 1e-9
                                  ),
                                  f = -a * (0.5 - 1e-9) + 0.5,
                                  d = Math.min(u, s),
                                  g = Math.floor(
                                    Math.log(e / d) / Math.log(t) + f
                                  ),
                                  _ = Math.max(p, g);
                                return mt(e / Math.pow(t, _), c, u);
                              }
                            };
                          })(a, n, i, h, !f && d, u)
                        : dl(n, i, h, !f && d, u));
                  }
                  return {
                    constraint: e,
                    maxResolution: n,
                    minResolution: i,
                    minZoom: r,
                    zoomFactor: a,
                  };
                })(t);
                (this.maxResolution_ = i.maxResolution),
                  (this.minResolution_ = i.minResolution),
                  (this.zoomFactor_ = i.zoomFactor),
                  (this.resolutions_ = t.resolutions),
                  (this.padding_ = t.padding),
                  (this.minZoom_ = i.minZoom);
                var r = (function (t) {
                    if (void 0 !== t.extent) {
                      var e =
                        void 0 === t.smoothExtentConstraint ||
                        t.smoothExtentConstraint;
                      return ul(t.extent, t.constrainOnlyCenter, e);
                    }
                    var n = Je(t.projection, "EPSG:3857");
                    if (!0 !== t.multiWorld && n.isGlobal()) {
                      var i = n.getExtent().slice();
                      return (i[0] = -1 / 0), (i[2] = 1 / 0), ul(i, !1, !1);
                    }
                    return cl;
                  })(t),
                  o = i.constraint,
                  a = (function (t) {
                    if (void 0 === t.enableRotation || t.enableRotation) {
                      var e = t.constrainRotation;
                      return void 0 === e || !0 === e
                        ? ((r = Et(5)),
                          function (t, e) {
                            return e
                              ? t
                              : void 0 !== t
                              ? Math.abs(t) <= r
                                ? 0
                                : t
                              : void 0;
                          })
                        : !1 === e
                        ? _l
                        : "number" == typeof e
                        ? ((n = e),
                          (i = (2 * Math.PI) / n),
                          function (t, e) {
                            return e
                              ? t
                              : void 0 !== t
                              ? (t = Math.floor(t / i + 0.5) * i)
                              : void 0;
                          })
                        : _l;
                    }
                    return gl;
                    var n, i, r;
                  })(t);
                (this.constraints_ = { center: r, resolution: o, rotation: a }),
                  this.setRotation(void 0 !== t.rotation ? t.rotation : 0),
                  this.setCenterInternal(void 0 !== t.center ? t.center : null),
                  void 0 !== t.resolution
                    ? this.setResolution(t.resolution)
                    : void 0 !== t.zoom && this.setZoom(t.zoom);
              }),
              Object.defineProperty(e.prototype, "padding", {
                get: function () {
                  return this.padding_;
                },
                set: function (t) {
                  var e = this.padding_;
                  this.padding_ = t;
                  var n = this.getCenter();
                  if (n) {
                    var i = t || [0, 0, 0, 0];
                    e = e || [0, 0, 0, 0];
                    var r = this.getResolution(),
                      o = (r / 2) * (i[3] - e[3] + e[1] - i[1]),
                      s = (r / 2) * (i[0] - e[0] + e[2] - i[2]);
                    this.setCenterInternal([n[0] + o, n[1] - s]);
                  }
                },
                enumerable: !1,
                configurable: !0,
              }),
              (e.prototype.getUpdatedOptions_ = function (t) {
                var e = this.getProperties();
                return (
                  void 0 !== e.resolution
                    ? (e.resolution = this.getResolution())
                    : (e.zoom = this.getZoom()),
                  (e.center = this.getCenterInternal()),
                  (e.rotation = this.getRotation()),
                  f({}, e, t)
                );
              }),
              (e.prototype.animate = function (t) {
                this.isDef() &&
                  !this.getAnimating() &&
                  this.resolveConstraints(0);
                for (
                  var e = new Array(arguments.length), n = 0;
                  n < e.length;
                  ++n
                ) {
                  var i = arguments[n];
                  i.center &&
                    ((i = f({}, i)).center = un(
                      i.center,
                      this.getProjection()
                    )),
                    i.anchor &&
                      ((i = f({}, i)).anchor = un(
                        i.anchor,
                        this.getProjection()
                      )),
                    (e[n] = i);
                }
                this.animateInternal.apply(this, e);
              }),
              (e.prototype.animateInternal = function (t) {
                var e,
                  n = arguments.length;
                n > 1 &&
                  "function" == typeof arguments[n - 1] &&
                  ((e = arguments[n - 1]), --n);
                for (var i = 0; i < n && !this.isDef(); ++i) {
                  var r = arguments[i];
                  r.center && this.setCenterInternal(r.center),
                    void 0 !== r.zoom
                      ? this.setZoom(r.zoom)
                      : r.resolution && this.setResolution(r.resolution),
                    void 0 !== r.rotation && this.setRotation(r.rotation);
                }
                if (i !== n) {
                  for (
                    var o = Date.now(),
                      s = this.targetCenter_.slice(),
                      a = this.targetResolution_,
                      l = this.targetRotation_,
                      h = [];
                    i < n;
                    ++i
                  ) {
                    var u = arguments[i],
                      c = {
                        start: o,
                        complete: !1,
                        anchor: u.anchor,
                        duration: void 0 !== u.duration ? u.duration : 1e3,
                        easing: u.easing || Cn,
                        callback: e,
                      };
                    if (
                      (u.center &&
                        ((c.sourceCenter = s),
                        (c.targetCenter = u.center.slice()),
                        (s = c.targetCenter)),
                      void 0 !== u.zoom
                        ? ((c.sourceResolution = a),
                          (c.targetResolution = this.getResolutionForZoom(
                            u.zoom
                          )),
                          (a = c.targetResolution))
                        : u.resolution &&
                          ((c.sourceResolution = a),
                          (c.targetResolution = u.resolution),
                          (a = c.targetResolution)),
                      void 0 !== u.rotation)
                    ) {
                      c.sourceRotation = l;
                      var p =
                        Tt(u.rotation - l + Math.PI, 2 * Math.PI) - Math.PI;
                      (c.targetRotation = l + p), (l = c.targetRotation);
                    }
                    xl(c) ? (c.complete = !0) : (o += c.duration), h.push(c);
                  }
                  this.animations_.push(h),
                    this.setHint(0, 1),
                    this.updateAnimations_();
                } else e && ml(e, !0);
              }),
              (e.prototype.getAnimating = function () {
                return this.hints_[0] > 0;
              }),
              (e.prototype.getInteracting = function () {
                return this.hints_[1] > 0;
              }),
              (e.prototype.cancelAnimations = function () {
                var t;
                this.setHint(0, -this.hints_[0]);
                for (var e = 0, n = this.animations_.length; e < n; ++e) {
                  var i = this.animations_[e];
                  if ((i[0].callback && ml(i[0].callback, !1), !t))
                    for (var r = 0, o = i.length; r < o; ++r) {
                      var s = i[r];
                      if (!s.complete) {
                        t = s.anchor;
                        break;
                      }
                    }
                }
                (this.animations_.length = 0),
                  (this.cancelAnchor_ = t),
                  (this.nextCenter_ = null),
                  (this.nextResolution_ = NaN),
                  (this.nextRotation_ = NaN);
              }),
              (e.prototype.updateAnimations_ = function () {
                if (
                  (void 0 !== this.updateAnimationKey_ &&
                    (cancelAnimationFrame(this.updateAnimationKey_),
                    (this.updateAnimationKey_ = void 0)),
                  this.getAnimating())
                ) {
                  for (
                    var t = Date.now(), e = !1, n = this.animations_.length - 1;
                    n >= 0;
                    --n
                  ) {
                    for (
                      var i = this.animations_[n], r = !0, o = 0, s = i.length;
                      o < s;
                      ++o
                    ) {
                      var a = i[o];
                      if (!a.complete) {
                        var l = t - a.start,
                          h = a.duration > 0 ? l / a.duration : 1;
                        h >= 1 ? ((a.complete = !0), (h = 1)) : (r = !1);
                        var u = a.easing(h);
                        if (a.sourceCenter) {
                          var c = a.sourceCenter[0],
                            p = a.sourceCenter[1],
                            f = a.targetCenter[0],
                            d = a.targetCenter[1];
                          this.nextCenter_ = a.targetCenter;
                          var g = c + u * (f - c),
                            _ = p + u * (d - p);
                          this.targetCenter_ = [g, _];
                        }
                        if (a.sourceResolution && a.targetResolution) {
                          var y =
                            1 === u
                              ? a.targetResolution
                              : a.sourceResolution +
                                u * (a.targetResolution - a.sourceResolution);
                          if (a.anchor) {
                            var v = this.getViewportSize_(this.getRotation()),
                              m = this.constraints_.resolution(y, 0, v, !0);
                            this.targetCenter_ = this.calculateCenterZoom(
                              m,
                              a.anchor
                            );
                          }
                          (this.nextResolution_ = a.targetResolution),
                            (this.targetResolution_ = y),
                            this.applyTargetState_(!0);
                        }
                        if (
                          void 0 !== a.sourceRotation &&
                          void 0 !== a.targetRotation
                        ) {
                          var x =
                            1 === u
                              ? Tt(a.targetRotation + Math.PI, 2 * Math.PI) -
                                Math.PI
                              : a.sourceRotation +
                                u * (a.targetRotation - a.sourceRotation);
                          if (a.anchor) {
                            var C = this.constraints_.rotation(x, !0);
                            this.targetCenter_ = this.calculateCenterRotate(
                              C,
                              a.anchor
                            );
                          }
                          (this.nextRotation_ = a.targetRotation),
                            (this.targetRotation_ = x);
                        }
                        if ((this.applyTargetState_(!0), (e = !0), !a.complete))
                          break;
                      }
                    }
                    if (r) {
                      (this.animations_[n] = null),
                        this.setHint(0, -1),
                        (this.nextCenter_ = null),
                        (this.nextResolution_ = NaN),
                        (this.nextRotation_ = NaN);
                      var w = i[0].callback;
                      w && ml(w, !0);
                    }
                  }
                  (this.animations_ = this.animations_.filter(Boolean)),
                    e &&
                      void 0 === this.updateAnimationKey_ &&
                      (this.updateAnimationKey_ = requestAnimationFrame(
                        this.updateAnimations_.bind(this)
                      ));
                }
              }),
              (e.prototype.calculateCenterRotate = function (t, e) {
                var n,
                  i,
                  r,
                  o = this.getCenterInternal();
                return (
                  void 0 !== o &&
                    (We(
                      (n = [o[0] - e[0], o[1] - e[1]]),
                      t - this.getRotation()
                    ),
                    (r = e),
                    ((i = n)[0] += +r[0]),
                    (i[1] += +r[1])),
                  n
                );
              }),
              (e.prototype.calculateCenterZoom = function (t, e) {
                var n,
                  i = this.getCenterInternal(),
                  r = this.getResolution();
                return (
                  void 0 !== i &&
                    void 0 !== r &&
                    (n = [
                      e[0] - (t * (e[0] - i[0])) / r,
                      e[1] - (t * (e[1] - i[1])) / r,
                    ]),
                  n
                );
              }),
              (e.prototype.getViewportSize_ = function (t) {
                var e = this.viewportSize_;
                if (t) {
                  var n = e[0],
                    i = e[1];
                  return [
                    Math.abs(n * Math.cos(t)) + Math.abs(i * Math.sin(t)),
                    Math.abs(n * Math.sin(t)) + Math.abs(i * Math.cos(t)),
                  ];
                }
                return e;
              }),
              (e.prototype.setViewportSize = function (t) {
                (this.viewportSize_ = Array.isArray(t)
                  ? t.slice()
                  : [100, 100]),
                  this.getAnimating() || this.resolveConstraints(0);
              }),
              (e.prototype.getCenter = function () {
                var t = this.getCenterInternal();
                return t ? hn(t, this.getProjection()) : t;
              }),
              (e.prototype.getCenterInternal = function () {
                return this.get(hl.CENTER);
              }),
              (e.prototype.getConstraints = function () {
                return this.constraints_;
              }),
              (e.prototype.getConstrainResolution = function () {
                return this.get("constrainResolution");
              }),
              (e.prototype.getHints = function (t) {
                return void 0 !== t
                  ? ((t[0] = this.hints_[0]), (t[1] = this.hints_[1]), t)
                  : this.hints_.slice();
              }),
              (e.prototype.calculateExtent = function (t) {
                return cn(
                  this.calculateExtentInternal(t),
                  this.getProjection()
                );
              }),
              (e.prototype.calculateExtentInternal = function (t) {
                var e = t || this.getViewportSizeMinusPadding_(),
                  n = this.getCenterInternal();
                vt(n, 1);
                var i = this.getResolution();
                vt(void 0 !== i, 2);
                var r = this.getRotation();
                return vt(void 0 !== r, 3), Me(n, i, r, e);
              }),
              (e.prototype.getMaxResolution = function () {
                return this.maxResolution_;
              }),
              (e.prototype.getMinResolution = function () {
                return this.minResolution_;
              }),
              (e.prototype.getMaxZoom = function () {
                return this.getZoomForResolution(this.minResolution_);
              }),
              (e.prototype.setMaxZoom = function (t) {
                this.applyOptions_(this.getUpdatedOptions_({ maxZoom: t }));
              }),
              (e.prototype.getMinZoom = function () {
                return this.getZoomForResolution(this.maxResolution_);
              }),
              (e.prototype.setMinZoom = function (t) {
                this.applyOptions_(this.getUpdatedOptions_({ minZoom: t }));
              }),
              (e.prototype.setConstrainResolution = function (t) {
                this.applyOptions_(
                  this.getUpdatedOptions_({ constrainResolution: t })
                );
              }),
              (e.prototype.getProjection = function () {
                return this.projection_;
              }),
              (e.prototype.getResolution = function () {
                return this.get(hl.RESOLUTION);
              }),
              (e.prototype.getResolutions = function () {
                return this.resolutions_;
              }),
              (e.prototype.getResolutionForExtent = function (t, e) {
                return this.getResolutionForExtentInternal(
                  pn(t, this.getProjection()),
                  e
                );
              }),
              (e.prototype.getResolutionForExtentInternal = function (t, e) {
                var n = e || this.getViewportSizeMinusPadding_(),
                  i = ke(t) / n[0],
                  r = Fe(t) / n[1];
                return Math.max(i, r);
              }),
              (e.prototype.getResolutionForValueFunction = function (t) {
                var e = t || 2,
                  n = this.getConstrainedResolution(this.maxResolution_),
                  i = this.minResolution_,
                  r = Math.log(n / i) / Math.log(e);
                return function (t) {
                  return n / Math.pow(e, t * r);
                };
              }),
              (e.prototype.getRotation = function () {
                return this.get(hl.ROTATION);
              }),
              (e.prototype.getValueForResolutionFunction = function (t) {
                var e = Math.log(t || 2),
                  n = this.getConstrainedResolution(this.maxResolution_),
                  i = this.minResolution_,
                  r = Math.log(n / i) / e;
                return function (t) {
                  return Math.log(n / t) / e / r;
                };
              }),
              (e.prototype.getViewportSizeMinusPadding_ = function (t) {
                var e = this.getViewportSize_(t),
                  n = this.padding_;
                return n && (e = [e[0] - n[1] - n[3], e[1] - n[0] - n[2]]), e;
              }),
              (e.prototype.getState = function () {
                var t = this.getProjection(),
                  e = this.getResolution(),
                  n = this.getRotation(),
                  i = this.getCenterInternal(),
                  r = this.padding_;
                if (r) {
                  var o = this.getViewportSizeMinusPadding_();
                  i = Cl(
                    i,
                    this.getViewportSize_(),
                    [o[0] / 2 + r[3], o[1] / 2 + r[0]],
                    e,
                    n
                  );
                }
                return {
                  center: i.slice(0),
                  projection: void 0 !== t ? t : null,
                  resolution: e,
                  nextCenter: this.nextCenter_,
                  nextResolution: this.nextResolution_,
                  nextRotation: this.nextRotation_,
                  rotation: n,
                  zoom: this.getZoom(),
                };
              }),
              (e.prototype.getZoom = function () {
                var t,
                  e = this.getResolution();
                return void 0 !== e && (t = this.getZoomForResolution(e)), t;
              }),
              (e.prototype.getZoomForResolution = function (t) {
                var e,
                  n,
                  i = this.minZoom_ || 0;
                if (this.resolutions_) {
                  var r = s(this.resolutions_, t, 1);
                  (i = r),
                    (e = this.resolutions_[r]),
                    (n =
                      r == this.resolutions_.length - 1
                        ? 2
                        : e / this.resolutions_[r + 1]);
                } else (e = this.maxResolution_), (n = this.zoomFactor_);
                return i + Math.log(e / t) / Math.log(n);
              }),
              (e.prototype.getResolutionForZoom = function (t) {
                if (this.resolutions_) {
                  if (this.resolutions_.length <= 1) return 0;
                  var e = mt(Math.floor(t), 0, this.resolutions_.length - 2),
                    n = this.resolutions_[e] / this.resolutions_[e + 1];
                  return this.resolutions_[e] / Math.pow(n, mt(t - e, 0, 1));
                }
                return (
                  this.maxResolution_ /
                  Math.pow(this.zoomFactor_, t - this.minZoom_)
                );
              }),
              (e.prototype.fit = function (t, e) {
                var n;
                if (
                  (vt(
                    Array.isArray(t) ||
                      "function" == typeof t.getSimplifiedGeometry,
                    24
                  ),
                  Array.isArray(t))
                )
                  vt(!Ge(t), 25), (n = Zi((i = pn(t, this.getProjection()))));
                else if (t.getType() === kn) {
                  var i;
                  (n = Zi(
                    (i = pn(t.getExtent(), this.getProjection()))
                  )).rotate(this.getRotation(), Ie(i));
                } else {
                  var r = ln();
                  n = r ? t.clone().transform(r, this.getProjection()) : t;
                }
                this.fitInternal(n, e);
              }),
              (e.prototype.rotatedExtentForGeometry = function (t) {
                for (
                  var e = this.getRotation(),
                    n = Math.cos(e),
                    i = Math.sin(-e),
                    r = t.getFlatCoordinates(),
                    o = t.getStride(),
                    s = 1 / 0,
                    a = 1 / 0,
                    l = -1 / 0,
                    h = -1 / 0,
                    u = 0,
                    c = r.length;
                  u < c;
                  u += o
                ) {
                  var p = r[u] * n - r[u + 1] * i,
                    f = r[u] * i + r[u + 1] * n;
                  (s = Math.min(s, p)),
                    (a = Math.min(a, f)),
                    (l = Math.max(l, p)),
                    (h = Math.max(h, f));
                }
                return [s, a, l, h];
              }),
              (e.prototype.fitInternal = function (t, e) {
                var n = e || {},
                  i = n.size;
                i || (i = this.getViewportSizeMinusPadding_());
                var r,
                  o = void 0 !== n.padding ? n.padding : [0, 0, 0, 0],
                  s = void 0 !== n.nearest && n.nearest;
                r =
                  void 0 !== n.minResolution
                    ? n.minResolution
                    : void 0 !== n.maxZoom
                    ? this.getResolutionForZoom(n.maxZoom)
                    : 0;
                var a = this.rotatedExtentForGeometry(t),
                  l = this.getResolutionForExtentInternal(a, [
                    i[0] - o[1] - o[3],
                    i[1] - o[0] - o[2],
                  ]);
                (l = isNaN(l) ? r : Math.max(l, r)),
                  (l = this.getConstrainedResolution(l, s ? 0 : 1));
                var h = this.getRotation(),
                  u = Math.sin(h),
                  c = Math.cos(h),
                  f = Ie(a);
                (f[0] += ((o[1] - o[3]) / 2) * l),
                  (f[1] += ((o[0] - o[2]) / 2) * l);
                var d = f[0] * c - f[1] * u,
                  g = f[1] * c + f[0] * u,
                  _ = this.getConstrainedCenter([d, g], l),
                  y = n.callback ? n.callback : p;
                void 0 !== n.duration
                  ? this.animateInternal(
                      {
                        resolution: l,
                        center: _,
                        duration: n.duration,
                        easing: n.easing,
                      },
                      y
                    )
                  : ((this.targetResolution_ = l),
                    (this.targetCenter_ = _),
                    this.applyTargetState_(!1, !0),
                    ml(y, !0));
              }),
              (e.prototype.centerOn = function (t, e, n) {
                this.centerOnInternal(un(t, this.getProjection()), e, n);
              }),
              (e.prototype.centerOnInternal = function (t, e, n) {
                this.setCenterInternal(
                  Cl(t, e, n, this.getResolution(), this.getRotation())
                );
              }),
              (e.prototype.calculateCenterShift = function (t, e, n, i) {
                var r,
                  o = this.padding_;
                if (o && t) {
                  var s = this.getViewportSizeMinusPadding_(-n),
                    a = Cl(t, i, [s[0] / 2 + o[3], s[1] / 2 + o[0]], e, n);
                  r = [t[0] - a[0], t[1] - a[1]];
                }
                return r;
              }),
              (e.prototype.isDef = function () {
                return (
                  !!this.getCenterInternal() && void 0 !== this.getResolution()
                );
              }),
              (e.prototype.adjustCenter = function (t) {
                var e = hn(this.targetCenter_, this.getProjection());
                this.setCenter([e[0] + t[0], e[1] + t[1]]);
              }),
              (e.prototype.adjustCenterInternal = function (t) {
                var e = this.targetCenter_;
                this.setCenterInternal([e[0] + t[0], e[1] + t[1]]);
              }),
              (e.prototype.adjustResolution = function (t, e) {
                var n = e && un(e, this.getProjection());
                this.adjustResolutionInternal(t, n);
              }),
              (e.prototype.adjustResolutionInternal = function (t, e) {
                var n = this.getAnimating() || this.getInteracting(),
                  i = this.getViewportSize_(this.getRotation()),
                  r = this.constraints_.resolution(
                    this.targetResolution_ * t,
                    0,
                    i,
                    n
                  );
                e && (this.targetCenter_ = this.calculateCenterZoom(r, e)),
                  (this.targetResolution_ *= t),
                  this.applyTargetState_();
              }),
              (e.prototype.adjustZoom = function (t, e) {
                this.adjustResolution(Math.pow(this.zoomFactor_, -t), e);
              }),
              (e.prototype.adjustRotation = function (t, e) {
                e && (e = un(e, this.getProjection())),
                  this.adjustRotationInternal(t, e);
              }),
              (e.prototype.adjustRotationInternal = function (t, e) {
                var n = this.getAnimating() || this.getInteracting(),
                  i = this.constraints_.rotation(this.targetRotation_ + t, n);
                e && (this.targetCenter_ = this.calculateCenterRotate(i, e)),
                  (this.targetRotation_ += t),
                  this.applyTargetState_();
              }),
              (e.prototype.setCenter = function (t) {
                this.setCenterInternal(t ? un(t, this.getProjection()) : t);
              }),
              (e.prototype.setCenterInternal = function (t) {
                (this.targetCenter_ = t), this.applyTargetState_();
              }),
              (e.prototype.setHint = function (t, e) {
                return (this.hints_[t] += e), this.changed(), this.hints_[t];
              }),
              (e.prototype.setResolution = function (t) {
                (this.targetResolution_ = t), this.applyTargetState_();
              }),
              (e.prototype.setRotation = function (t) {
                (this.targetRotation_ = t), this.applyTargetState_();
              }),
              (e.prototype.setZoom = function (t) {
                this.setResolution(this.getResolutionForZoom(t));
              }),
              (e.prototype.applyTargetState_ = function (t, e) {
                var n = this.getAnimating() || this.getInteracting() || e,
                  i = this.constraints_.rotation(this.targetRotation_, n),
                  r = this.getViewportSize_(i),
                  o = this.constraints_.resolution(
                    this.targetResolution_,
                    0,
                    r,
                    n
                  ),
                  s = this.constraints_.center(
                    this.targetCenter_,
                    o,
                    r,
                    n,
                    this.calculateCenterShift(this.targetCenter_, o, i, r)
                  );
                this.get(hl.ROTATION) !== i && this.set(hl.ROTATION, i),
                  this.get(hl.RESOLUTION) !== o &&
                    (this.set(hl.RESOLUTION, o),
                    this.set("zoom", this.getZoom(), !0)),
                  (s && this.get(hl.CENTER) && ze(this.get(hl.CENTER), s)) ||
                    this.set(hl.CENTER, s),
                  this.getAnimating() && !t && this.cancelAnimations(),
                  (this.cancelAnchor_ = void 0);
              }),
              (e.prototype.resolveConstraints = function (t, e, n) {
                var i = void 0 !== t ? t : 200,
                  r = e || 0,
                  o = this.constraints_.rotation(this.targetRotation_),
                  s = this.getViewportSize_(o),
                  a = this.constraints_.resolution(
                    this.targetResolution_,
                    r,
                    s
                  ),
                  l = this.constraints_.center(
                    this.targetCenter_,
                    a,
                    s,
                    !1,
                    this.calculateCenterShift(this.targetCenter_, a, o, s)
                  );
                if (0 === i && !this.cancelAnchor_)
                  return (
                    (this.targetResolution_ = a),
                    (this.targetRotation_ = o),
                    (this.targetCenter_ = l),
                    void this.applyTargetState_()
                  );
                var h = n || (0 === i ? this.cancelAnchor_ : void 0);
                (this.cancelAnchor_ = void 0),
                  (this.getResolution() === a &&
                    this.getRotation() === o &&
                    this.getCenterInternal() &&
                    ze(this.getCenterInternal(), l)) ||
                    (this.getAnimating() && this.cancelAnimations(),
                    this.animateInternal({
                      rotation: o,
                      center: l,
                      resolution: a,
                      duration: i,
                      easing: xn,
                      anchor: h,
                    }));
              }),
              (e.prototype.beginInteraction = function () {
                this.resolveConstraints(0), this.setHint(1, 1);
              }),
              (e.prototype.endInteraction = function (t, e, n) {
                var i = n && un(n, this.getProjection());
                this.endInteractionInternal(t, e, i);
              }),
              (e.prototype.endInteractionInternal = function (t, e, n) {
                this.setHint(1, -1), this.resolveConstraints(t, e, n);
              }),
              (e.prototype.getConstrainedCenter = function (t, e) {
                var n = this.getViewportSize_(this.getRotation());
                return this.constraints_.center(
                  t,
                  e || this.getResolution(),
                  n
                );
              }),
              (e.prototype.getConstrainedZoom = function (t, e) {
                var n = this.getResolutionForZoom(t);
                return this.getZoomForResolution(
                  this.getConstrainedResolution(n, e)
                );
              }),
              (e.prototype.getConstrainedResolution = function (t, e) {
                var n = e || 0,
                  i = this.getViewportSize_(this.getRotation());
                return this.constraints_.resolution(t, n, i);
              }),
              e
            );
          })(G);
        function ml(t, e) {
          setTimeout(function () {
            t(e);
          }, 0);
        }
        function xl(t) {
          return (
            !(
              t.sourceCenter &&
              t.targetCenter &&
              !ze(t.sourceCenter, t.targetCenter)
            ) &&
            t.sourceResolution === t.targetResolution &&
            t.sourceRotation === t.targetRotation
          );
        }
        function Cl(t, e, n, i, r) {
          var o = Math.cos(-r),
            s = Math.sin(-r),
            a = t[0] * o - t[1] * s,
            l = t[1] * o + t[0] * s;
          return [
            (a += (e[0] / 2 - n[0]) * i) * o -
              (l += (n[1] - e[1] / 2) * i) * (s = -s),
            l * o + a * s,
          ];
        }
        var wl = vl,
          Sl = (function () {
            var t = function (e, n) {
              return (
                (t =
                  Object.setPrototypeOf ||
                  ({ __proto__: [] } instanceof Array &&
                    function (t, e) {
                      t.__proto__ = e;
                    }) ||
                  function (t, e) {
                    for (var n in e)
                      Object.prototype.hasOwnProperty.call(e, n) &&
                        (t[n] = e[n]);
                  }),
                t(e, n)
              );
            };
            return function (e, n) {
              if ("function" != typeof n && null !== n)
                throw new TypeError(
                  "Class extends value " +
                    String(n) +
                    " is not a constructor or null"
                );
              function i() {
                this.constructor = e;
              }
              t(e, n),
                (e.prototype =
                  null === n
                    ? Object.create(n)
                    : ((i.prototype = n.prototype), new i()));
            };
          })();
        function El(t) {
          t instanceof Gt
            ? t.setMapInternal(null)
            : t instanceof Va && t.getLayers().forEach(El);
        }
        function Tl(t, e) {
          if (t instanceof Gt) t.setMapInternal(e);
          else if (t instanceof Va)
            for (
              var n = t.getLayers().getArray(), i = 0, r = n.length;
              i < r;
              ++i
            )
              Tl(n[i], e);
        }
        var bl = (function (t) {
            function n(e) {
              var n = t.call(this) || this;
              n.on, n.once, n.un;
              var i = (function (t) {
                var e = null;
                void 0 !== t.keyboardEventTarget &&
                  (e =
                    "string" == typeof t.keyboardEventTarget
                      ? document.getElementById(t.keyboardEventTarget)
                      : t.keyboardEventTarget);
                var n,
                  i,
                  r,
                  o = {},
                  s =
                    t.layers && "function" == typeof t.layers.getLayers
                      ? t.layers
                      : new Va({ layers: t.layers });
                return (
                  (o.layergroup = s),
                  (o.target = t.target),
                  (o.view = t.view instanceof wl ? t.view : new wl()),
                  void 0 !== t.controls &&
                    (Array.isArray(t.controls)
                      ? (n = new Ea(t.controls.slice()))
                      : (vt("function" == typeof t.controls.getArray, 47),
                        (n = t.controls))),
                  void 0 !== t.interactions &&
                    (Array.isArray(t.interactions)
                      ? (i = new Ea(t.interactions.slice()))
                      : (vt("function" == typeof t.interactions.getArray, 48),
                        (i = t.interactions))),
                  void 0 !== t.overlays
                    ? Array.isArray(t.overlays)
                      ? (r = new Ea(t.overlays.slice()))
                      : (vt("function" == typeof t.overlays.getArray, 49),
                        (r = t.overlays))
                    : (r = new Ea()),
                  {
                    controls: n,
                    interactions: i,
                    keyboardEventTarget: e,
                    overlays: r,
                    values: o,
                  }
                );
              })(e);
              n.renderComplete_,
                (n.loaded_ = !0),
                (n.boundHandleBrowserEvent_ = n.handleBrowserEvent.bind(n)),
                (n.maxTilesLoading_ =
                  void 0 !== e.maxTilesLoading ? e.maxTilesLoading : 16),
                (n.pixelRatio_ = void 0 !== e.pixelRatio ? e.pixelRatio : Z),
                n.postRenderTimeoutHandle_,
                n.animationDelayKey_,
                (n.animationDelay_ = function () {
                  (this.animationDelayKey_ = void 0),
                    this.renderFrame_(Date.now());
                }.bind(n)),
                (n.coordinateToPixelTransform_ = [1, 0, 0, 1, 0, 0]),
                (n.pixelToCoordinateTransform_ = [1, 0, 0, 1, 0, 0]),
                (n.frameIndex_ = 0),
                (n.frameState_ = null),
                (n.previousExtent_ = null),
                (n.viewPropertyListenerKey_ = null),
                (n.viewChangeListenerKey_ = null),
                (n.layerGroupPropertyListenerKeys_ = null),
                (n.viewport_ = document.createElement("div")),
                (n.viewport_.className =
                  "ol-viewport" +
                  ("ontouchstart" in window ? " ol-touch" : "")),
                (n.viewport_.style.position = "relative"),
                (n.viewport_.style.overflow = "hidden"),
                (n.viewport_.style.width = "100%"),
                (n.viewport_.style.height = "100%"),
                (n.overlayContainer_ = document.createElement("div")),
                (n.overlayContainer_.style.position = "absolute"),
                (n.overlayContainer_.style.zIndex = "0"),
                (n.overlayContainer_.style.width = "100%"),
                (n.overlayContainer_.style.height = "100%"),
                (n.overlayContainer_.style.pointerEvents = "none"),
                (n.overlayContainer_.className = "ol-overlaycontainer"),
                n.viewport_.appendChild(n.overlayContainer_),
                (n.overlayContainerStopEvent_ = document.createElement("div")),
                (n.overlayContainerStopEvent_.style.position = "absolute"),
                (n.overlayContainerStopEvent_.style.zIndex = "0"),
                (n.overlayContainerStopEvent_.style.width = "100%"),
                (n.overlayContainerStopEvent_.style.height = "100%"),
                (n.overlayContainerStopEvent_.style.pointerEvents = "none"),
                (n.overlayContainerStopEvent_.className =
                  "ol-overlaycontainer-stopevent"),
                n.viewport_.appendChild(n.overlayContainerStopEvent_),
                (n.mapBrowserEventHandler_ = null),
                (n.moveTolerance_ = e.moveTolerance),
                (n.keyboardEventTarget_ = i.keyboardEventTarget),
                (n.targetChangeHandlerKeys_ = null),
                (n.controls = i.controls || new Ea()),
                (n.interactions = i.interactions || new Ea()),
                (n.overlays_ = i.overlays),
                (n.overlayIdIndex_ = {}),
                (n.renderer_ = null),
                (n.postRenderFunctions_ = []),
                (n.tileQueue_ = new ll(
                  n.getTilePriority.bind(n),
                  n.handleTileChange_.bind(n)
                )),
                n.addChangeListener(el, n.handleLayerGroupChanged_),
                n.addChangeListener(rl, n.handleViewChanged_),
                n.addChangeListener(nl, n.handleSizeChanged_),
                n.addChangeListener(il, n.handleTargetChanged_),
                n.setProperties(i.values);
              var r = n;
              return (
                !e.view ||
                  e.view instanceof wl ||
                  e.view.then(function (t) {
                    r.setView(new wl(t));
                  }),
                n.controls.addEventListener(
                  ma,
                  function (t) {
                    t.element.setMap(this);
                  }.bind(n)
                ),
                n.controls.addEventListener(
                  xa,
                  function (t) {
                    t.element.setMap(null);
                  }.bind(n)
                ),
                n.interactions.addEventListener(
                  ma,
                  function (t) {
                    t.element.setMap(this);
                  }.bind(n)
                ),
                n.interactions.addEventListener(
                  xa,
                  function (t) {
                    t.element.setMap(null);
                  }.bind(n)
                ),
                n.overlays_.addEventListener(
                  ma,
                  function (t) {
                    this.addOverlayInternal_(t.element);
                  }.bind(n)
                ),
                n.overlays_.addEventListener(
                  xa,
                  function (t) {
                    var e = t.element.getId();
                    void 0 !== e && delete this.overlayIdIndex_[e.toString()],
                      t.element.setMap(null);
                  }.bind(n)
                ),
                n.controls.forEach(
                  function (t) {
                    t.setMap(this);
                  }.bind(n)
                ),
                n.interactions.forEach(
                  function (t) {
                    t.setMap(this);
                  }.bind(n)
                ),
                n.overlays_.forEach(n.addOverlayInternal_.bind(n)),
                n
              );
            }
            return (
              Sl(n, t),
              (n.prototype.createRenderer = function () {
                throw new Error(
                  "Use a map type that has a createRenderer method"
                );
              }),
              (n.prototype.addControl = function (t) {
                this.getControls().push(t);
              }),
              (n.prototype.addInteraction = function (t) {
                this.getInteractions().push(t);
              }),
              (n.prototype.addLayer = function (t) {
                this.getLayerGroup().getLayers().push(t);
              }),
              (n.prototype.handleLayerAdd_ = function (t) {
                Tl(t.layer, this);
              }),
              (n.prototype.addOverlay = function (t) {
                this.getOverlays().push(t);
              }),
              (n.prototype.addOverlayInternal_ = function (t) {
                var e = t.getId();
                void 0 !== e && (this.overlayIdIndex_[e.toString()] = t),
                  t.setMap(this);
              }),
              (n.prototype.disposeInternal = function () {
                this.setTarget(null), t.prototype.disposeInternal.call(this);
              }),
              (n.prototype.forEachFeatureAtPixel = function (t, e, n) {
                if (this.frameState_ && this.renderer_) {
                  var i = this.getCoordinateFromPixelInternal(t),
                    r =
                      void 0 !== (n = void 0 !== n ? n : {}).hitTolerance
                        ? n.hitTolerance
                        : 0,
                    o = void 0 !== n.layerFilter ? n.layerFilter : u,
                    s = !1 !== n.checkWrapped;
                  return this.renderer_.forEachFeatureAtCoordinate(
                    i,
                    this.frameState_,
                    r,
                    s,
                    e,
                    null,
                    o,
                    null
                  );
                }
              }),
              (n.prototype.getFeaturesAtPixel = function (t, e) {
                var n = [];
                return (
                  this.forEachFeatureAtPixel(
                    t,
                    function (t) {
                      n.push(t);
                    },
                    e
                  ),
                  n
                );
              }),
              (n.prototype.getAllLayers = function () {
                var t = [];
                return (
                  (function e(n) {
                    n.forEach(function (n) {
                      n instanceof Va ? e(n.getLayers()) : t.push(n);
                    });
                  })(this.getLayers()),
                  t
                );
              }),
              (n.prototype.forEachLayerAtPixel = function (t, e, n) {
                if (this.frameState_ && this.renderer_) {
                  var i = n || {},
                    r = void 0 !== i.hitTolerance ? i.hitTolerance : 0,
                    o = i.layerFilter || u;
                  return this.renderer_.forEachLayerAtPixel(
                    t,
                    this.frameState_,
                    r,
                    e,
                    o
                  );
                }
              }),
              (n.prototype.hasFeatureAtPixel = function (t, e) {
                if (!this.frameState_ || !this.renderer_) return !1;
                var n = this.getCoordinateFromPixelInternal(t),
                  i =
                    void 0 !== (e = void 0 !== e ? e : {}).layerFilter
                      ? e.layerFilter
                      : u,
                  r = void 0 !== e.hitTolerance ? e.hitTolerance : 0,
                  o = !1 !== e.checkWrapped;
                return this.renderer_.hasFeatureAtCoordinate(
                  n,
                  this.frameState_,
                  r,
                  o,
                  i,
                  null
                );
              }),
              (n.prototype.getEventCoordinate = function (t) {
                return this.getCoordinateFromPixel(this.getEventPixel(t));
              }),
              (n.prototype.getEventCoordinateInternal = function (t) {
                return this.getCoordinateFromPixelInternal(
                  this.getEventPixel(t)
                );
              }),
              (n.prototype.getEventPixel = function (t) {
                var e = this.viewport_.getBoundingClientRect(),
                  n = "changedTouches" in t ? t.changedTouches[0] : t;
                return [n.clientX - e.left, n.clientY - e.top];
              }),
              (n.prototype.getTarget = function () {
                return this.get(il);
              }),
              (n.prototype.getTargetElement = function () {
                var t = this.getTarget();
                return void 0 !== t
                  ? "string" == typeof t
                    ? document.getElementById(t)
                    : t
                  : null;
              }),
              (n.prototype.getCoordinateFromPixel = function (t) {
                return hn(
                  this.getCoordinateFromPixelInternal(t),
                  this.getView().getProjection()
                );
              }),
              (n.prototype.getCoordinateFromPixelInternal = function (t) {
                var e = this.frameState_;
                return e ? jn(e.pixelToCoordinateTransform, t.slice()) : null;
              }),
              (n.prototype.getControls = function () {
                return this.controls;
              }),
              (n.prototype.getOverlays = function () {
                return this.overlays_;
              }),
              (n.prototype.getOverlayById = function (t) {
                var e = this.overlayIdIndex_[t.toString()];
                return void 0 !== e ? e : null;
              }),
              (n.prototype.getInteractions = function () {
                return this.interactions;
              }),
              (n.prototype.getLayerGroup = function () {
                return this.get(el);
              }),
              (n.prototype.setLayers = function (t) {
                var e = this.getLayerGroup();
                if (t instanceof Ea) e.setLayers(t);
                else {
                  var n = e.getLayers();
                  n.clear(), n.extend(t);
                }
              }),
              (n.prototype.getLayers = function () {
                return this.getLayerGroup().getLayers();
              }),
              (n.prototype.getLoadingOrNotReady = function () {
                for (
                  var t = this.getLayerGroup().getLayerStatesArray(),
                    e = 0,
                    n = t.length;
                  e < n;
                  ++e
                ) {
                  var i = t[e];
                  if (i.visible) {
                    var r = i.layer.getRenderer();
                    if (r && !r.ready) return !0;
                    var o = i.layer.getSource();
                    if (o && o.loading) return !0;
                  }
                }
                return !1;
              }),
              (n.prototype.getPixelFromCoordinate = function (t) {
                var e = un(t, this.getView().getProjection());
                return this.getPixelFromCoordinateInternal(e);
              }),
              (n.prototype.getPixelFromCoordinateInternal = function (t) {
                var e = this.frameState_;
                return e
                  ? jn(e.coordinateToPixelTransform, t.slice(0, 2))
                  : null;
              }),
              (n.prototype.getRenderer = function () {
                return this.renderer_;
              }),
              (n.prototype.getSize = function () {
                return this.get(nl);
              }),
              (n.prototype.getView = function () {
                return this.get(rl);
              }),
              (n.prototype.getViewport = function () {
                return this.viewport_;
              }),
              (n.prototype.getOverlayContainer = function () {
                return this.overlayContainer_;
              }),
              (n.prototype.getOverlayContainerStopEvent = function () {
                return this.overlayContainerStopEvent_;
              }),
              (n.prototype.getOwnerDocument = function () {
                var t = this.getTargetElement();
                return t ? t.ownerDocument : document;
              }),
              (n.prototype.getTilePriority = function (t, e, n, i) {
                return (function (t, e, n, i, r) {
                  if (!t || !(n in t.wantedTiles)) return ol;
                  if (!t.wantedTiles[n][e.getKey()]) return ol;
                  var o = t.viewState.center,
                    s = i[0] - o[0],
                    a = i[1] - o[1];
                  return 65536 * Math.log(r) + Math.sqrt(s * s + a * a) / r;
                })(this.frameState_, t, e, n, i);
              }),
              (n.prototype.handleBrowserEvent = function (t, e) {
                var n = e || t.type,
                  i = new Ja(n, this, t);
                this.handleMapBrowserEvent(i);
              }),
              (n.prototype.handleMapBrowserEvent = function (t) {
                if (this.frameState_) {
                  var e = t.originalEvent,
                    n = e.type;
                  if (n === Nt || n === b || n === S) {
                    var i = this.getOwnerDocument(),
                      r = this.viewport_.getRootNode
                        ? this.viewport_.getRootNode()
                        : i,
                      o = e.target;
                    if (
                      this.overlayContainerStopEvent_.contains(o) ||
                      !(r === i ? i.documentElement : r).contains(o)
                    )
                      return;
                  }
                  if (
                    ((t.frameState = this.frameState_),
                    !1 !== this.dispatchEvent(t))
                  )
                    for (
                      var s = this.getInteractions().getArray().slice(),
                        a = s.length - 1;
                      a >= 0;
                      a--
                    ) {
                      var l = s[a];
                      if (
                        l.getMap() === this &&
                        l.getActive() &&
                        this.getTargetElement() &&
                        (!l.handleEvent(t) || t.propagationStopped)
                      )
                        break;
                    }
                }
              }),
              (n.prototype.handlePostRender = function () {
                var t = this.frameState_,
                  e = this.tileQueue_;
                if (!e.isEmpty()) {
                  var n = this.maxTilesLoading_,
                    i = n;
                  if (t) {
                    var r = t.viewHints;
                    if (r[0] || r[1]) {
                      var o = Date.now() - t.time > 8;
                      (n = o ? 0 : 8), (i = o ? 0 : 2);
                    }
                  }
                  e.getTilesLoading() < n &&
                    (e.reprioritize(), e.loadMoreTiles(n, i));
                }
                t &&
                  this.renderer_ &&
                  !t.animate &&
                  (!0 === this.renderComplete_
                    ? (this.hasListener(Lt) &&
                        this.renderer_.dispatchRenderEvent(Lt, t),
                      !1 === this.loaded_ &&
                        ((this.loaded_ = !0),
                        this.dispatchEvent(new Ha(X, this, t))))
                    : !0 === this.loaded_ &&
                      ((this.loaded_ = !1),
                      this.dispatchEvent(new Ha(W, this, t))));
                for (
                  var s = this.postRenderFunctions_, a = 0, l = s.length;
                  a < l;
                  ++a
                )
                  s[a](this, t);
                s.length = 0;
              }),
              (n.prototype.handleSizeChanged_ = function () {
                this.getView() &&
                  !this.getView().getAnimating() &&
                  this.getView().resolveConstraints(0),
                  this.render();
              }),
              (n.prototype.handleTargetChanged_ = function () {
                if (this.mapBrowserEventHandler_) {
                  for (
                    var t = 0, e = this.targetChangeHandlerKeys_.length;
                    t < e;
                    ++t
                  )
                    I(this.targetChangeHandlerKeys_[t]);
                  (this.targetChangeHandlerKeys_ = null),
                    this.viewport_.removeEventListener(
                      C,
                      this.boundHandleBrowserEvent_
                    ),
                    this.viewport_.removeEventListener(
                      b,
                      this.boundHandleBrowserEvent_
                    ),
                    this.mapBrowserEventHandler_.dispose(),
                    (this.mapBrowserEventHandler_ = null),
                    Q(this.viewport_);
                }
                var n = this.getTargetElement();
                if (n) {
                  for (var i in (n.appendChild(this.viewport_),
                  this.renderer_ || (this.renderer_ = this.createRenderer()),
                  (this.mapBrowserEventHandler_ = new tl(
                    this,
                    this.moveTolerance_
                  )),
                  Qa))
                    this.mapBrowserEventHandler_.addEventListener(
                      Qa[i],
                      this.handleMapBrowserEvent.bind(this)
                    );
                  this.viewport_.addEventListener(
                    C,
                    this.boundHandleBrowserEvent_,
                    !1
                  ),
                    this.viewport_.addEventListener(
                      b,
                      this.boundHandleBrowserEvent_,
                      !!H && { passive: !1 }
                    );
                  var r = this.getOwnerDocument().defaultView,
                    o = this.keyboardEventTarget_
                      ? this.keyboardEventTarget_
                      : n;
                  this.targetChangeHandlerKeys_ = [
                    O(o, S, this.handleBrowserEvent, this),
                    O(o, E, this.handleBrowserEvent, this),
                    O(r, "resize", this.updateSize, this),
                  ];
                } else
                  this.renderer_ &&
                    (clearTimeout(this.postRenderTimeoutHandle_),
                    (this.postRenderTimeoutHandle_ = void 0),
                    (this.postRenderFunctions_.length = 0),
                    this.renderer_.dispose(),
                    (this.renderer_ = null)),
                    this.animationDelayKey_ &&
                      (cancelAnimationFrame(this.animationDelayKey_),
                      (this.animationDelayKey_ = void 0));
                this.updateSize();
              }),
              (n.prototype.handleTileChange_ = function () {
                this.render();
              }),
              (n.prototype.handleViewPropertyChanged_ = function () {
                this.render();
              }),
              (n.prototype.handleViewChanged_ = function () {
                this.viewPropertyListenerKey_ &&
                  (I(this.viewPropertyListenerKey_),
                  (this.viewPropertyListenerKey_ = null)),
                  this.viewChangeListenerKey_ &&
                    (I(this.viewChangeListenerKey_),
                    (this.viewChangeListenerKey_ = null));
                var t = this.getView();
                t &&
                  (this.updateViewportSize_(),
                  (this.viewPropertyListenerKey_ = O(
                    t,
                    e,
                    this.handleViewPropertyChanged_,
                    this
                  )),
                  (this.viewChangeListenerKey_ = O(
                    t,
                    x,
                    this.handleViewPropertyChanged_,
                    this
                  )),
                  t.resolveConstraints(0)),
                  this.render();
              }),
              (n.prototype.handleLayerGroupChanged_ = function () {
                this.layerGroupPropertyListenerKeys_ &&
                  (this.layerGroupPropertyListenerKeys_.forEach(I),
                  (this.layerGroupPropertyListenerKeys_ = null));
                var t = this.getLayerGroup();
                t &&
                  (this.handleLayerAdd_(new Ka("addlayer", t)),
                  (this.layerGroupPropertyListenerKeys_ = [
                    O(t, e, this.render, this),
                    O(t, x, this.render, this),
                    O(t, "addlayer", this.handleLayerAdd_, this),
                    O(t, "removelayer", this.handleLayerRemove_, this),
                  ])),
                  this.render();
              }),
              (n.prototype.isRendered = function () {
                return !!this.frameState_;
              }),
              (n.prototype.renderSync = function () {
                this.animationDelayKey_ &&
                  cancelAnimationFrame(this.animationDelayKey_),
                  this.animationDelay_();
              }),
              (n.prototype.redrawText = function () {
                for (
                  var t = this.getLayerGroup().getLayerStatesArray(),
                    e = 0,
                    n = t.length;
                  e < n;
                  ++e
                ) {
                  var i = t[e].layer;
                  i.hasRenderer() && i.getRenderer().handleFontsChanged();
                }
              }),
              (n.prototype.render = function () {
                this.renderer_ &&
                  void 0 === this.animationDelayKey_ &&
                  (this.animationDelayKey_ = requestAnimationFrame(
                    this.animationDelay_
                  ));
              }),
              (n.prototype.removeControl = function (t) {
                return this.getControls().remove(t);
              }),
              (n.prototype.removeInteraction = function (t) {
                return this.getInteractions().remove(t);
              }),
              (n.prototype.removeLayer = function (t) {
                return this.getLayerGroup().getLayers().remove(t);
              }),
              (n.prototype.handleLayerRemove_ = function (t) {
                El(t.layer);
              }),
              (n.prototype.removeOverlay = function (t) {
                return this.getOverlays().remove(t);
              }),
              (n.prototype.renderFrame_ = function (t) {
                var e = this,
                  n = this.getSize(),
                  i = this.getView(),
                  r = this.frameState_,
                  o = null;
                if (void 0 !== n && Dr(n) && i && i.isDef()) {
                  var s = i.getHints(
                      this.frameState_ ? this.frameState_.viewHints : void 0
                    ),
                    a = i.getState();
                  if (
                    ((o = {
                      animate: !1,
                      coordinateToPixelTransform:
                        this.coordinateToPixelTransform_,
                      declutterTree: null,
                      extent: Me(a.center, a.resolution, a.rotation, n),
                      index: this.frameIndex_++,
                      layerIndex: 0,
                      layerStatesArray:
                        this.getLayerGroup().getLayerStatesArray(),
                      pixelRatio: this.pixelRatio_,
                      pixelToCoordinateTransform:
                        this.pixelToCoordinateTransform_,
                      postRenderFunctions: [],
                      size: n,
                      tileQueue: this.tileQueue_,
                      time: t,
                      usedTiles: {},
                      viewState: a,
                      viewHints: s,
                      wantedTiles: {},
                      mapId: D(this),
                      renderTargets: {},
                    }),
                    a.nextCenter && a.nextResolution)
                  ) {
                    var l = isNaN(a.nextRotation) ? a.rotation : a.nextRotation;
                    o.nextExtent = Me(a.nextCenter, a.nextResolution, l, n);
                  }
                }
                (this.frameState_ = o),
                  this.renderer_.renderFrame(o),
                  o &&
                    (o.animate && this.render(),
                    Array.prototype.push.apply(
                      this.postRenderFunctions_,
                      o.postRenderFunctions
                    ),
                    r &&
                      (!this.previousExtent_ ||
                        (!Ge(this.previousExtent_) &&
                          !Ce(o.extent, this.previousExtent_))) &&
                      (this.dispatchEvent(new Ha("movestart", this, r)),
                      (this.previousExtent_ = me(this.previousExtent_))),
                    this.previousExtent_ &&
                      !o.viewHints[0] &&
                      !o.viewHints[1] &&
                      !Ce(o.extent, this.previousExtent_) &&
                      (this.dispatchEvent(new Ha("moveend", this, o)),
                      pe(o.extent, this.previousExtent_))),
                  this.dispatchEvent(new Ha(z, this, o)),
                  (this.renderComplete_ =
                    this.hasListener(W) ||
                    this.hasListener(X) ||
                    this.hasListener(Lt)
                      ? !this.tileQueue_.getTilesLoading() &&
                        !this.tileQueue_.getCount() &&
                        !this.getLoadingOrNotReady()
                      : void 0),
                  this.postRenderTimeoutHandle_ ||
                    (this.postRenderTimeoutHandle_ = setTimeout(function () {
                      (e.postRenderTimeoutHandle_ = void 0),
                        e.handlePostRender();
                    }, 0));
              }),
              (n.prototype.setLayerGroup = function (t) {
                var e = this.getLayerGroup();
                e && this.handleLayerRemove_(new Ka("removelayer", e)),
                  this.set(el, t);
              }),
              (n.prototype.setSize = function (t) {
                this.set(nl, t);
              }),
              (n.prototype.setTarget = function (t) {
                this.set(il, t);
              }),
              (n.prototype.setView = function (t) {
                if (!t || t instanceof wl) this.set(rl, t);
                else {
                  this.set(rl, new wl());
                  var e = this;
                  t.then(function (t) {
                    e.setView(new wl(t));
                  });
                }
              }),
              (n.prototype.updateSize = function () {
                var t = this.getTargetElement(),
                  e = void 0;
                if (t) {
                  var n = getComputedStyle(t),
                    i =
                      t.offsetWidth -
                      parseFloat(n.borderLeftWidth) -
                      parseFloat(n.paddingLeft) -
                      parseFloat(n.paddingRight) -
                      parseFloat(n.borderRightWidth),
                    r =
                      t.offsetHeight -
                      parseFloat(n.borderTopWidth) -
                      parseFloat(n.paddingTop) -
                      parseFloat(n.paddingBottom) -
                      parseFloat(n.borderBottomWidth);
                  isNaN(i) ||
                    isNaN(r) ||
                    (!Dr((e = [i, r])) &&
                      (t.offsetWidth ||
                        t.offsetHeight ||
                        t.getClientRects().length) &&
                      console.warn(
                        "No map visible because the map container's width or height are 0."
                      ));
                }
                this.setSize(e), this.updateViewportSize_();
              }),
              (n.prototype.updateViewportSize_ = function () {
                var t = this.getView();
                if (t) {
                  var e = void 0,
                    n = getComputedStyle(this.viewport_);
                  n.width &&
                    n.height &&
                    (e = [parseInt(n.width, 10), parseInt(n.height, 10)]),
                    t.setViewportSize(e);
                }
              }),
              n
            );
          })(G),
          Ol = (function () {
            var t = function (e, n) {
              return (
                (t =
                  Object.setPrototypeOf ||
                  ({ __proto__: [] } instanceof Array &&
                    function (t, e) {
                      t.__proto__ = e;
                    }) ||
                  function (t, e) {
                    for (var n in e)
                      Object.prototype.hasOwnProperty.call(e, n) &&
                        (t[n] = e[n]);
                  }),
                t(e, n)
              );
            };
            return function (e, n) {
              if ("function" != typeof n && null !== n)
                throw new TypeError(
                  "Class extends value " +
                    String(n) +
                    " is not a constructor or null"
                );
              function i() {
                this.constructor = e;
              }
              t(e, n),
                (e.prototype =
                  null === n
                    ? Object.create(n)
                    : ((i.prototype = n.prototype), new i()));
            };
          })(),
          Rl = (function (t) {
            function e(e) {
              var n = this,
                i = e || {};
              n =
                t.call(this, {
                  element: document.createElement("div"),
                  render: i.render,
                  target: i.target,
                }) || this;
              var r = void 0 !== i.className ? i.className : "ol-rotate",
                o = void 0 !== i.label ? i.label : "⇧",
                s =
                  void 0 !== i.compassClassName
                    ? i.compassClassName
                    : "ol-compass";
              (n.label_ = null),
                "string" == typeof o
                  ? ((n.label_ = document.createElement("span")),
                    (n.label_.className = s),
                    (n.label_.textContent = o))
                  : ((n.label_ = o), n.label_.classList.add(s));
              var a = i.tipLabel ? i.tipLabel : "Reset rotation",
                l = document.createElement("button");
              (l.className = r + "-reset"),
                l.setAttribute("type", "button"),
                (l.title = a),
                l.appendChild(n.label_),
                l.addEventListener(w, n.handleClick_.bind(n), !1);
              var h = r + " ol-unselectable " + nt,
                u = n.element;
              return (
                (u.className = h),
                u.appendChild(l),
                (n.callResetNorth_ = i.resetNorth ? i.resetNorth : void 0),
                (n.duration_ = void 0 !== i.duration ? i.duration : 250),
                (n.autoHide_ = void 0 === i.autoHide || i.autoHide),
                (n.rotation_ = void 0),
                n.autoHide_ && n.element.classList.add(et),
                n
              );
            }
            return (
              Ol(e, t),
              (e.prototype.handleClick_ = function (t) {
                t.preventDefault(),
                  void 0 !== this.callResetNorth_
                    ? this.callResetNorth_()
                    : this.resetNorth_();
              }),
              (e.prototype.resetNorth_ = function () {
                var t = this.getMap().getView();
                if (t) {
                  var e = t.getRotation();
                  void 0 !== e &&
                    (this.duration_ > 0 && e % (2 * Math.PI) != 0
                      ? t.animate({
                          rotation: 0,
                          duration: this.duration_,
                          easing: xn,
                        })
                      : t.setRotation(0));
                }
              }),
              (e.prototype.render = function (t) {
                var e = t.frameState;
                if (e) {
                  var n = e.viewState.rotation;
                  if (n != this.rotation_) {
                    var i = "rotate(" + n + "rad)";
                    if (this.autoHide_) {
                      var r = this.element.classList.contains(et);
                      r || 0 !== n
                        ? r && 0 !== n && this.element.classList.remove(et)
                        : this.element.classList.add(et);
                    }
                    this.label_.style.transform = i;
                  }
                  this.rotation_ = n;
                }
              }),
              e
            );
          })(tt),
          Il = Rl,
          Pl = "active",
          Ml = (function () {
            var t = function (e, n) {
              return (
                (t =
                  Object.setPrototypeOf ||
                  ({ __proto__: [] } instanceof Array &&
                    function (t, e) {
                      t.__proto__ = e;
                    }) ||
                  function (t, e) {
                    for (var n in e)
                      Object.prototype.hasOwnProperty.call(e, n) &&
                        (t[n] = e[n]);
                  }),
                t(e, n)
              );
            };
            return function (e, n) {
              if ("function" != typeof n && null !== n)
                throw new TypeError(
                  "Class extends value " +
                    String(n) +
                    " is not a constructor or null"
                );
              function i() {
                this.constructor = e;
              }
              t(e, n),
                (e.prototype =
                  null === n
                    ? Object.create(n)
                    : ((i.prototype = n.prototype), new i()));
            };
          })();
        function Fl(t, e, n, i) {
          var r = t.getZoom();
          if (void 0 !== r) {
            var o = t.getConstrainedZoom(r + e),
              s = t.getResolutionForZoom(o);
            t.getAnimating() && t.cancelAnimations(),
              t.animate({
                resolution: s,
                anchor: n,
                duration: void 0 !== i ? i : 250,
                easing: xn,
              });
          }
        }
        var Ll = (function (t) {
            function e(e) {
              var n = t.call(this) || this;
              return (
                n.on,
                n.once,
                n.un,
                e && e.handleEvent && (n.handleEvent = e.handleEvent),
                (n.map_ = null),
                n.setActive(!0),
                n
              );
            }
            return (
              Ml(e, t),
              (e.prototype.getActive = function () {
                return this.get(Pl);
              }),
              (e.prototype.getMap = function () {
                return this.map_;
              }),
              (e.prototype.handleEvent = function (t) {
                return !0;
              }),
              (e.prototype.setActive = function (t) {
                this.set(Pl, t);
              }),
              (e.prototype.setMap = function (t) {
                this.map_ = t;
              }),
              e
            );
          })(G),
          Al = (function () {
            var t = function (e, n) {
              return (
                (t =
                  Object.setPrototypeOf ||
                  ({ __proto__: [] } instanceof Array &&
                    function (t, e) {
                      t.__proto__ = e;
                    }) ||
                  function (t, e) {
                    for (var n in e)
                      Object.prototype.hasOwnProperty.call(e, n) &&
                        (t[n] = e[n]);
                  }),
                t(e, n)
              );
            };
            return function (e, n) {
              if ("function" != typeof n && null !== n)
                throw new TypeError(
                  "Class extends value " +
                    String(n) +
                    " is not a constructor or null"
                );
              function i() {
                this.constructor = e;
              }
              t(e, n),
                (e.prototype =
                  null === n
                    ? Object.create(n)
                    : ((i.prototype = n.prototype), new i()));
            };
          })(),
          Dl = (function (t) {
            function e(e) {
              var n = t.call(this) || this,
                i = e || {};
              return (
                (n.delta_ = i.delta ? i.delta : 1),
                (n.duration_ = void 0 !== i.duration ? i.duration : 250),
                n
              );
            }
            return (
              Al(e, t),
              (e.prototype.handleEvent = function (t) {
                var e = !1;
                if (t.type == Qa.DBLCLICK) {
                  var n = t.originalEvent,
                    i = t.map,
                    r = t.coordinate,
                    o = n.shiftKey ? -this.delta_ : this.delta_;
                  Fl(i.getView(), o, r, this.duration_),
                    n.preventDefault(),
                    (e = !0);
                }
                return !e;
              }),
              e
            );
          })(Ll),
          kl = (function () {
            var t = function (e, n) {
              return (
                (t =
                  Object.setPrototypeOf ||
                  ({ __proto__: [] } instanceof Array &&
                    function (t, e) {
                      t.__proto__ = e;
                    }) ||
                  function (t, e) {
                    for (var n in e)
                      Object.prototype.hasOwnProperty.call(e, n) &&
                        (t[n] = e[n]);
                  }),
                t(e, n)
              );
            };
            return function (e, n) {
              if ("function" != typeof n && null !== n)
                throw new TypeError(
                  "Class extends value " +
                    String(n) +
                    " is not a constructor or null"
                );
              function i() {
                this.constructor = e;
              }
              t(e, n),
                (e.prototype =
                  null === n
                    ? Object.create(n)
                    : ((i.prototype = n.prototype), new i()));
            };
          })();
        function jl(t) {
          for (var e = t.length, n = 0, i = 0, r = 0; r < e; r++)
            (n += t[r].clientX), (i += t[r].clientY);
          return [n / e, i / e];
        }
        var Gl = (function (t) {
          function e(e) {
            var n = this,
              i = e || {};
            return (
              (n = t.call(this, i) || this),
              i.handleDownEvent && (n.handleDownEvent = i.handleDownEvent),
              i.handleDragEvent && (n.handleDragEvent = i.handleDragEvent),
              i.handleMoveEvent && (n.handleMoveEvent = i.handleMoveEvent),
              i.handleUpEvent && (n.handleUpEvent = i.handleUpEvent),
              i.stopDown && (n.stopDown = i.stopDown),
              (n.handlingDownUpSequence = !1),
              (n.trackedPointers_ = {}),
              (n.targetPointers = []),
              n
            );
          }
          return (
            kl(e, t),
            (e.prototype.getPointerCount = function () {
              return this.targetPointers.length;
            }),
            (e.prototype.handleDownEvent = function (t) {
              return !1;
            }),
            (e.prototype.handleDragEvent = function (t) {}),
            (e.prototype.handleEvent = function (t) {
              if (!t.originalEvent) return !0;
              var e = !1;
              if (
                (this.updateTrackedPointers_(t), this.handlingDownUpSequence)
              ) {
                if (t.type == Qa.POINTERDRAG)
                  this.handleDragEvent(t), t.originalEvent.preventDefault();
                else if (t.type == Qa.POINTERUP) {
                  var n = this.handleUpEvent(t);
                  this.handlingDownUpSequence =
                    n && this.targetPointers.length > 0;
                }
              } else if (t.type == Qa.POINTERDOWN) {
                var i = this.handleDownEvent(t);
                (this.handlingDownUpSequence = i), (e = this.stopDown(i));
              } else t.type == Qa.POINTERMOVE && this.handleMoveEvent(t);
              return !e;
            }),
            (e.prototype.handleMoveEvent = function (t) {}),
            (e.prototype.handleUpEvent = function (t) {
              return !1;
            }),
            (e.prototype.stopDown = function (t) {
              return t;
            }),
            (e.prototype.updateTrackedPointers_ = function (t) {
              if (
                (function (t) {
                  var e = t.type;
                  return (
                    e === Qa.POINTERDOWN ||
                    e === Qa.POINTERDRAG ||
                    e === Qa.POINTERUP
                  );
                })(t)
              ) {
                var e = t.originalEvent,
                  n = e.pointerId.toString();
                t.type == Qa.POINTERUP
                  ? delete this.trackedPointers_[n]
                  : (t.type == Qa.POINTERDOWN || n in this.trackedPointers_) &&
                    (this.trackedPointers_[n] = e),
                  (this.targetPointers = g(this.trackedPointers_));
              }
            }),
            e
          );
        })(Ll);
        function zl(t) {
          var e = arguments;
          return function (t) {
            for (
              var n = !0, i = 0, r = e.length;
              i < r && (n = n && e[i](t));
              ++i
            );
            return n;
          };
        }
        var Wl = function (t) {
            var e = t.originalEvent;
            return e.altKey && !(e.metaKey || e.ctrlKey) && e.shiftKey;
          },
          Xl = function (t) {
            return (
              !t.map.getTargetElement().hasAttribute("tabindex") ||
              (function (t) {
                var e = t.map.getTargetElement(),
                  n = t.map.getOwnerDocument().activeElement;
                return e.contains(n);
              })(t)
            );
          },
          Nl = u,
          Yl = function (t) {
            var e = t.originalEvent;
            return 0 == e.button && !(B && K && e.ctrlKey);
          },
          Bl = function (t) {
            var e = t.originalEvent;
            return !e.altKey && !(e.metaKey || e.ctrlKey) && !e.shiftKey;
          },
          Kl = function (t) {
            var e = t.originalEvent;
            return !e.altKey && !(e.metaKey || e.ctrlKey) && e.shiftKey;
          },
          Zl = function (t) {
            var e = t.originalEvent.target.tagName;
            return "INPUT" !== e && "SELECT" !== e && "TEXTAREA" !== e;
          },
          Vl = function (t) {
            var e = t.originalEvent;
            return vt(void 0 !== e, 56), "mouse" == e.pointerType;
          },
          Ul = function (t) {
            var e = t.originalEvent;
            return vt(void 0 !== e, 56), e.isPrimary && 0 === e.button;
          },
          Hl = (function () {
            var t = function (e, n) {
              return (
                (t =
                  Object.setPrototypeOf ||
                  ({ __proto__: [] } instanceof Array &&
                    function (t, e) {
                      t.__proto__ = e;
                    }) ||
                  function (t, e) {
                    for (var n in e)
                      Object.prototype.hasOwnProperty.call(e, n) &&
                        (t[n] = e[n]);
                  }),
                t(e, n)
              );
            };
            return function (e, n) {
              if ("function" != typeof n && null !== n)
                throw new TypeError(
                  "Class extends value " +
                    String(n) +
                    " is not a constructor or null"
                );
              function i() {
                this.constructor = e;
              }
              t(e, n),
                (e.prototype =
                  null === n
                    ? Object.create(n)
                    : ((i.prototype = n.prototype), new i()));
            };
          })(),
          ql = (function (t) {
            function e(e) {
              var n = t.call(this, { stopDown: c }) || this,
                i = e || {};
              (n.kinetic_ = i.kinetic),
                (n.lastCentroid = null),
                n.lastPointersCount_,
                (n.panning_ = !1);
              var r = i.condition ? i.condition : zl(Bl, Ul);
              return (
                (n.condition_ = i.onFocusOnly ? zl(Xl, r) : r),
                (n.noKinetic_ = !1),
                n
              );
            }
            return (
              Hl(e, t),
              (e.prototype.handleDragEvent = function (t) {
                this.panning_ ||
                  ((this.panning_ = !0),
                  this.getMap().getView().beginInteraction());
                var e,
                  n,
                  i = this.targetPointers,
                  r = jl(i);
                if (i.length == this.lastPointersCount_) {
                  if (
                    (this.kinetic_ && this.kinetic_.update(r[0], r[1]),
                    this.lastCentroid)
                  ) {
                    var o = [
                        this.lastCentroid[0] - r[0],
                        r[1] - this.lastCentroid[1],
                      ],
                      s = t.map.getView();
                    (e = o),
                      (n = s.getResolution()),
                      (e[0] *= n),
                      (e[1] *= n),
                      We(o, s.getRotation()),
                      s.adjustCenterInternal(o);
                  }
                } else this.kinetic_ && this.kinetic_.begin();
                (this.lastCentroid = r),
                  (this.lastPointersCount_ = i.length),
                  t.originalEvent.preventDefault();
              }),
              (e.prototype.handleUpEvent = function (t) {
                var e = t.map,
                  n = e.getView();
                if (0 === this.targetPointers.length) {
                  if (
                    !this.noKinetic_ &&
                    this.kinetic_ &&
                    this.kinetic_.end()
                  ) {
                    var i = this.kinetic_.getDistance(),
                      r = this.kinetic_.getAngle(),
                      o = n.getCenterInternal(),
                      s = e.getPixelFromCoordinateInternal(o),
                      a = e.getCoordinateFromPixelInternal([
                        s[0] - i * Math.cos(r),
                        s[1] - i * Math.sin(r),
                      ]);
                    n.animateInternal({
                      center: n.getConstrainedCenter(a),
                      duration: 500,
                      easing: xn,
                    });
                  }
                  return (
                    this.panning_ && ((this.panning_ = !1), n.endInteraction()),
                    !1
                  );
                }
                return (
                  this.kinetic_ && this.kinetic_.begin(),
                  (this.lastCentroid = null),
                  !0
                );
              }),
              (e.prototype.handleDownEvent = function (t) {
                if (this.targetPointers.length > 0 && this.condition_(t)) {
                  var e = t.map.getView();
                  return (
                    (this.lastCentroid = null),
                    e.getAnimating() && e.cancelAnimations(),
                    this.kinetic_ && this.kinetic_.begin(),
                    (this.noKinetic_ = this.targetPointers.length > 1),
                    !0
                  );
                }
                return !1;
              }),
              e
            );
          })(Gl),
          Jl = (function () {
            var t = function (e, n) {
              return (
                (t =
                  Object.setPrototypeOf ||
                  ({ __proto__: [] } instanceof Array &&
                    function (t, e) {
                      t.__proto__ = e;
                    }) ||
                  function (t, e) {
                    for (var n in e)
                      Object.prototype.hasOwnProperty.call(e, n) &&
                        (t[n] = e[n]);
                  }),
                t(e, n)
              );
            };
            return function (e, n) {
              if ("function" != typeof n && null !== n)
                throw new TypeError(
                  "Class extends value " +
                    String(n) +
                    " is not a constructor or null"
                );
              function i() {
                this.constructor = e;
              }
              t(e, n),
                (e.prototype =
                  null === n
                    ? Object.create(n)
                    : ((i.prototype = n.prototype), new i()));
            };
          })(),
          Ql = (function (t) {
            function e(e) {
              var n = this,
                i = e || {};
              return (
                ((n = t.call(this, { stopDown: c }) || this).condition_ =
                  i.condition ? i.condition : Wl),
                (n.lastAngle_ = void 0),
                (n.duration_ = void 0 !== i.duration ? i.duration : 250),
                n
              );
            }
            return (
              Jl(e, t),
              (e.prototype.handleDragEvent = function (t) {
                if (Vl(t)) {
                  var e = t.map,
                    n = e.getView();
                  if (n.getConstraints().rotation !== gl) {
                    var i = e.getSize(),
                      r = t.pixel,
                      o = Math.atan2(i[1] / 2 - r[1], r[0] - i[0] / 2);
                    if (void 0 !== this.lastAngle_) {
                      var s = o - this.lastAngle_;
                      n.adjustRotationInternal(-s);
                    }
                    this.lastAngle_ = o;
                  }
                }
              }),
              (e.prototype.handleUpEvent = function (t) {
                return (
                  !Vl(t) || (t.map.getView().endInteraction(this.duration_), !1)
                );
              }),
              (e.prototype.handleDownEvent = function (t) {
                return !(
                  !Vl(t) ||
                  !Yl(t) ||
                  !this.condition_(t) ||
                  (t.map.getView().beginInteraction(),
                  (this.lastAngle_ = void 0),
                  0)
                );
              }),
              e
            );
          })(Gl),
          $l = (function () {
            var t = function (e, n) {
              return (
                (t =
                  Object.setPrototypeOf ||
                  ({ __proto__: [] } instanceof Array &&
                    function (t, e) {
                      t.__proto__ = e;
                    }) ||
                  function (t, e) {
                    for (var n in e)
                      Object.prototype.hasOwnProperty.call(e, n) &&
                        (t[n] = e[n]);
                  }),
                t(e, n)
              );
            };
            return function (e, n) {
              if ("function" != typeof n && null !== n)
                throw new TypeError(
                  "Class extends value " +
                    String(n) +
                    " is not a constructor or null"
                );
              function i() {
                this.constructor = e;
              }
              t(e, n),
                (e.prototype =
                  null === n
                    ? Object.create(n)
                    : ((i.prototype = n.prototype), new i()));
            };
          })(),
          th = (function (t) {
            function e(e) {
              var n = t.call(this) || this;
              return (
                (n.geometry_ = null),
                (n.element_ = document.createElement("div")),
                (n.element_.style.position = "absolute"),
                (n.element_.style.pointerEvents = "auto"),
                (n.element_.className = "ol-box " + e),
                (n.map_ = null),
                (n.startPixel_ = null),
                (n.endPixel_ = null),
                n
              );
            }
            return (
              $l(e, t),
              (e.prototype.disposeInternal = function () {
                this.setMap(null);
              }),
              (e.prototype.render_ = function () {
                var t = this.startPixel_,
                  e = this.endPixel_,
                  n = "px",
                  i = this.element_.style;
                (i.left = Math.min(t[0], e[0]) + n),
                  (i.top = Math.min(t[1], e[1]) + n),
                  (i.width = Math.abs(e[0] - t[0]) + n),
                  (i.height = Math.abs(e[1] - t[1]) + n);
              }),
              (e.prototype.setMap = function (t) {
                if (this.map_) {
                  this.map_.getOverlayContainer().removeChild(this.element_);
                  var e = this.element_.style;
                  (e.left = "inherit"),
                    (e.top = "inherit"),
                    (e.width = "inherit"),
                    (e.height = "inherit");
                }
                (this.map_ = t),
                  this.map_ &&
                    this.map_.getOverlayContainer().appendChild(this.element_);
              }),
              (e.prototype.setPixels = function (t, e) {
                (this.startPixel_ = t),
                  (this.endPixel_ = e),
                  this.createOrUpdateGeometry(),
                  this.render_();
              }),
              (e.prototype.createOrUpdateGeometry = function () {
                var t = this.startPixel_,
                  e = this.endPixel_,
                  n = [t, [t[0], e[1]], e, [e[0], t[1]]].map(
                    this.map_.getCoordinateFromPixelInternal,
                    this.map_
                  );
                (n[4] = n[0].slice()),
                  this.geometry_
                    ? this.geometry_.setCoordinates([n])
                    : (this.geometry_ = new Ki([n]));
              }),
              (e.prototype.getGeometry = function () {
                return this.geometry_;
              }),
              e
            );
          })(r),
          eh = th,
          nh = (function () {
            var t = function (e, n) {
              return (
                (t =
                  Object.setPrototypeOf ||
                  ({ __proto__: [] } instanceof Array &&
                    function (t, e) {
                      t.__proto__ = e;
                    }) ||
                  function (t, e) {
                    for (var n in e)
                      Object.prototype.hasOwnProperty.call(e, n) &&
                        (t[n] = e[n]);
                  }),
                t(e, n)
              );
            };
            return function (e, n) {
              if ("function" != typeof n && null !== n)
                throw new TypeError(
                  "Class extends value " +
                    String(n) +
                    " is not a constructor or null"
                );
              function i() {
                this.constructor = e;
              }
              t(e, n),
                (e.prototype =
                  null === n
                    ? Object.create(n)
                    : ((i.prototype = n.prototype), new i()));
            };
          })(),
          ih = (function (t) {
            function e(e, n, i) {
              var r = t.call(this, e) || this;
              return (r.coordinate = n), (r.mapBrowserEvent = i), r;
            }
            return nh(e, t), e;
          })(t),
          rh = (function (t) {
            function e(e) {
              var n = t.call(this) || this;
              n.on, n.once, n.un;
              var i = e || {};
              return (
                (n.box_ = new eh(i.className || "ol-dragbox")),
                (n.minArea_ = void 0 !== i.minArea ? i.minArea : 64),
                i.onBoxEnd && (n.onBoxEnd = i.onBoxEnd),
                (n.startPixel_ = null),
                (n.condition_ = i.condition ? i.condition : Yl),
                (n.boxEndCondition_ = i.boxEndCondition
                  ? i.boxEndCondition
                  : n.defaultBoxEndCondition),
                n
              );
            }
            return (
              nh(e, t),
              (e.prototype.defaultBoxEndCondition = function (t, e, n) {
                var i = n[0] - e[0],
                  r = n[1] - e[1];
                return i * i + r * r >= this.minArea_;
              }),
              (e.prototype.getGeometry = function () {
                return this.box_.getGeometry();
              }),
              (e.prototype.handleDragEvent = function (t) {
                this.box_.setPixels(this.startPixel_, t.pixel),
                  this.dispatchEvent(new ih("boxdrag", t.coordinate, t));
              }),
              (e.prototype.handleUpEvent = function (t) {
                this.box_.setMap(null);
                var e = this.boxEndCondition_(t, this.startPixel_, t.pixel);
                return (
                  e && this.onBoxEnd(t),
                  this.dispatchEvent(
                    new ih(e ? "boxend" : "boxcancel", t.coordinate, t)
                  ),
                  !1
                );
              }),
              (e.prototype.handleDownEvent = function (t) {
                return (
                  !!this.condition_(t) &&
                  ((this.startPixel_ = t.pixel),
                  this.box_.setMap(t.map),
                  this.box_.setPixels(this.startPixel_, this.startPixel_),
                  this.dispatchEvent(new ih("boxstart", t.coordinate, t)),
                  !0)
                );
              }),
              (e.prototype.onBoxEnd = function (t) {}),
              e
            );
          })(Gl),
          oh = (function () {
            var t = function (e, n) {
              return (
                (t =
                  Object.setPrototypeOf ||
                  ({ __proto__: [] } instanceof Array &&
                    function (t, e) {
                      t.__proto__ = e;
                    }) ||
                  function (t, e) {
                    for (var n in e)
                      Object.prototype.hasOwnProperty.call(e, n) &&
                        (t[n] = e[n]);
                  }),
                t(e, n)
              );
            };
            return function (e, n) {
              if ("function" != typeof n && null !== n)
                throw new TypeError(
                  "Class extends value " +
                    String(n) +
                    " is not a constructor or null"
                );
              function i() {
                this.constructor = e;
              }
              t(e, n),
                (e.prototype =
                  null === n
                    ? Object.create(n)
                    : ((i.prototype = n.prototype), new i()));
            };
          })(),
          sh = (function (t) {
            function e(e) {
              var n = this,
                i = e || {},
                r = i.condition ? i.condition : Kl;
              return (
                ((n =
                  t.call(this, {
                    condition: r,
                    className: i.className || "ol-dragzoom",
                    minArea: i.minArea,
                  }) || this).duration_ =
                  void 0 !== i.duration ? i.duration : 200),
                (n.out_ = void 0 !== i.out && i.out),
                n
              );
            }
            return (
              oh(e, t),
              (e.prototype.onBoxEnd = function (t) {
                var e = this.getMap().getView(),
                  n = this.getGeometry();
                if (this.out_) {
                  var i = e.rotatedExtentForGeometry(n),
                    r = e.getResolutionForExtentInternal(i),
                    o = e.getResolution() / r;
                  (n = n.clone()).scale(o * o);
                }
                e.fitInternal(n, { duration: this.duration_, easing: xn });
              }),
              e
            );
          })(rh),
          ah = (function () {
            var t = function (e, n) {
              return (
                (t =
                  Object.setPrototypeOf ||
                  ({ __proto__: [] } instanceof Array &&
                    function (t, e) {
                      t.__proto__ = e;
                    }) ||
                  function (t, e) {
                    for (var n in e)
                      Object.prototype.hasOwnProperty.call(e, n) &&
                        (t[n] = e[n]);
                  }),
                t(e, n)
              );
            };
            return function (e, n) {
              if ("function" != typeof n && null !== n)
                throw new TypeError(
                  "Class extends value " +
                    String(n) +
                    " is not a constructor or null"
                );
              function i() {
                this.constructor = e;
              }
              t(e, n),
                (e.prototype =
                  null === n
                    ? Object.create(n)
                    : ((i.prototype = n.prototype), new i()));
            };
          })(),
          lh = (function (t) {
            function e(e) {
              var n = t.call(this) || this,
                i = e || {};
              return (
                (n.defaultCondition_ = function (t) {
                  return Bl(t) && Zl(t);
                }),
                (n.condition_ =
                  void 0 !== i.condition ? i.condition : n.defaultCondition_),
                (n.duration_ = void 0 !== i.duration ? i.duration : 100),
                (n.pixelDelta_ = void 0 !== i.pixelDelta ? i.pixelDelta : 128),
                n
              );
            }
            return (
              ah(e, t),
              (e.prototype.handleEvent = function (t) {
                var e = !1;
                if (t.type == S) {
                  var n = t.originalEvent,
                    i = n.keyCode;
                  if (
                    this.condition_(t) &&
                    (40 == i || 37 == i || 39 == i || 38 == i)
                  ) {
                    var r = t.map.getView(),
                      o = r.getResolution() * this.pixelDelta_,
                      s = 0,
                      a = 0;
                    40 == i
                      ? (a = -o)
                      : 37 == i
                      ? (s = -o)
                      : 39 == i
                      ? (s = o)
                      : (a = o);
                    var l = [s, a];
                    We(l, r.getRotation()),
                      (function (t, e, n) {
                        var i = t.getCenterInternal();
                        if (i) {
                          var r = [i[0] + e[0], i[1] + e[1]];
                          t.animateInternal({
                            duration: void 0 !== n ? n : 250,
                            easing: wn,
                            center: t.getConstrainedCenter(r),
                          });
                        }
                      })(r, l, this.duration_),
                      n.preventDefault(),
                      (e = !0);
                  }
                }
                return !e;
              }),
              e
            );
          })(Ll),
          hh = (function () {
            var t = function (e, n) {
              return (
                (t =
                  Object.setPrototypeOf ||
                  ({ __proto__: [] } instanceof Array &&
                    function (t, e) {
                      t.__proto__ = e;
                    }) ||
                  function (t, e) {
                    for (var n in e)
                      Object.prototype.hasOwnProperty.call(e, n) &&
                        (t[n] = e[n]);
                  }),
                t(e, n)
              );
            };
            return function (e, n) {
              if ("function" != typeof n && null !== n)
                throw new TypeError(
                  "Class extends value " +
                    String(n) +
                    " is not a constructor or null"
                );
              function i() {
                this.constructor = e;
              }
              t(e, n),
                (e.prototype =
                  null === n
                    ? Object.create(n)
                    : ((i.prototype = n.prototype), new i()));
            };
          })(),
          uh = (function (t) {
            function e(e) {
              var n = t.call(this) || this,
                i = e || {};
              return (
                (n.condition_ = i.condition ? i.condition : Zl),
                (n.delta_ = i.delta ? i.delta : 1),
                (n.duration_ = void 0 !== i.duration ? i.duration : 100),
                n
              );
            }
            return (
              hh(e, t),
              (e.prototype.handleEvent = function (t) {
                var e = !1;
                if (t.type == S || t.type == E) {
                  var n = t.originalEvent,
                    i = n.charCode;
                  if (
                    this.condition_(t) &&
                    (i == "+".charCodeAt(0) || i == "-".charCodeAt(0))
                  ) {
                    var r = t.map,
                      o = i == "+".charCodeAt(0) ? this.delta_ : -this.delta_;
                    Fl(r.getView(), o, void 0, this.duration_),
                      n.preventDefault(),
                      (e = !0);
                  }
                }
                return !e;
              }),
              e
            );
          })(Ll),
          ch = (function () {
            function t(t, e, n) {
              (this.decay_ = t),
                (this.minVelocity_ = e),
                (this.delay_ = n),
                (this.points_ = []),
                (this.angle_ = 0),
                (this.initialVelocity_ = 0);
            }
            return (
              (t.prototype.begin = function () {
                (this.points_.length = 0),
                  (this.angle_ = 0),
                  (this.initialVelocity_ = 0);
              }),
              (t.prototype.update = function (t, e) {
                this.points_.push(t, e, Date.now());
              }),
              (t.prototype.end = function () {
                if (this.points_.length < 6) return !1;
                var t = Date.now() - this.delay_,
                  e = this.points_.length - 3;
                if (this.points_[e + 2] < t) return !1;
                for (var n = e - 3; n > 0 && this.points_[n + 2] > t; ) n -= 3;
                var i = this.points_[e + 2] - this.points_[n + 2];
                if (i < 1e3 / 60) return !1;
                var r = this.points_[e] - this.points_[n],
                  o = this.points_[e + 1] - this.points_[n + 1];
                return (
                  (this.angle_ = Math.atan2(o, r)),
                  (this.initialVelocity_ = Math.sqrt(r * r + o * o) / i),
                  this.initialVelocity_ > this.minVelocity_
                );
              }),
              (t.prototype.getDistance = function () {
                return (
                  (this.minVelocity_ - this.initialVelocity_) / this.decay_
                );
              }),
              (t.prototype.getAngle = function () {
                return this.angle_;
              }),
              t
            );
          })(),
          ph = (function () {
            var t = function (e, n) {
              return (
                (t =
                  Object.setPrototypeOf ||
                  ({ __proto__: [] } instanceof Array &&
                    function (t, e) {
                      t.__proto__ = e;
                    }) ||
                  function (t, e) {
                    for (var n in e)
                      Object.prototype.hasOwnProperty.call(e, n) &&
                        (t[n] = e[n]);
                  }),
                t(e, n)
              );
            };
            return function (e, n) {
              if ("function" != typeof n && null !== n)
                throw new TypeError(
                  "Class extends value " +
                    String(n) +
                    " is not a constructor or null"
                );
              function i() {
                this.constructor = e;
              }
              t(e, n),
                (e.prototype =
                  null === n
                    ? Object.create(n)
                    : ((i.prototype = n.prototype), new i()));
            };
          })(),
          fh = "trackpad",
          dh = (function (t) {
            function e(e) {
              var n = this,
                i = e || {};
              ((n = t.call(this, i) || this).totalDelta_ = 0),
                (n.lastDelta_ = 0),
                (n.maxDelta_ = void 0 !== i.maxDelta ? i.maxDelta : 1),
                (n.duration_ = void 0 !== i.duration ? i.duration : 250),
                (n.timeout_ = void 0 !== i.timeout ? i.timeout : 80),
                (n.useAnchor_ = void 0 === i.useAnchor || i.useAnchor),
                (n.constrainResolution_ =
                  void 0 !== i.constrainResolution && i.constrainResolution);
              var r = i.condition ? i.condition : Nl;
              return (
                (n.condition_ = i.onFocusOnly ? zl(Xl, r) : r),
                (n.lastAnchor_ = null),
                (n.startTime_ = void 0),
                n.timeoutId_,
                (n.mode_ = void 0),
                (n.trackpadEventGap_ = 400),
                n.trackpadTimeoutId_,
                (n.deltaPerZoom_ = 300),
                n
              );
            }
            return (
              ph(e, t),
              (e.prototype.endInteraction_ = function () {
                (this.trackpadTimeoutId_ = void 0),
                  this.getMap()
                    .getView()
                    .endInteraction(
                      void 0,
                      this.lastDelta_ ? (this.lastDelta_ > 0 ? 1 : -1) : 0,
                      this.lastAnchor_
                    );
              }),
              (e.prototype.handleEvent = function (t) {
                if (!this.condition_(t)) return !0;
                if (t.type !== b) return !0;
                var e,
                  n = t.map,
                  i = t.originalEvent;
                if (
                  (i.preventDefault(),
                  this.useAnchor_ && (this.lastAnchor_ = t.coordinate),
                  t.type == b &&
                    ((e = i.deltaY),
                    Y && i.deltaMode === WheelEvent.DOM_DELTA_PIXEL && (e /= Z),
                    i.deltaMode === WheelEvent.DOM_DELTA_LINE && (e *= 40)),
                  0 === e)
                )
                  return !1;
                this.lastDelta_ = e;
                var r = Date.now();
                void 0 === this.startTime_ && (this.startTime_ = r),
                  (!this.mode_ ||
                    r - this.startTime_ > this.trackpadEventGap_) &&
                    (this.mode_ = Math.abs(e) < 4 ? fh : "wheel");
                var o = n.getView();
                if (
                  this.mode_ === fh &&
                  !o.getConstrainResolution() &&
                  !this.constrainResolution_
                )
                  return (
                    this.trackpadTimeoutId_
                      ? clearTimeout(this.trackpadTimeoutId_)
                      : (o.getAnimating() && o.cancelAnimations(),
                        o.beginInteraction()),
                    (this.trackpadTimeoutId_ = setTimeout(
                      this.endInteraction_.bind(this),
                      this.timeout_
                    )),
                    o.adjustZoom(-e / this.deltaPerZoom_, this.lastAnchor_),
                    (this.startTime_ = r),
                    !1
                  );
                this.totalDelta_ += e;
                var s = Math.max(this.timeout_ - (r - this.startTime_), 0);
                return (
                  clearTimeout(this.timeoutId_),
                  (this.timeoutId_ = setTimeout(
                    this.handleWheelZoom_.bind(this, n),
                    s
                  )),
                  !1
                );
              }),
              (e.prototype.handleWheelZoom_ = function (t) {
                var e = t.getView();
                e.getAnimating() && e.cancelAnimations();
                var n =
                  -mt(
                    this.totalDelta_,
                    -this.maxDelta_ * this.deltaPerZoom_,
                    this.maxDelta_ * this.deltaPerZoom_
                  ) / this.deltaPerZoom_;
                (e.getConstrainResolution() || this.constrainResolution_) &&
                  (n = n ? (n > 0 ? 1 : -1) : 0),
                  Fl(e, n, this.lastAnchor_, this.duration_),
                  (this.mode_ = void 0),
                  (this.totalDelta_ = 0),
                  (this.lastAnchor_ = null),
                  (this.startTime_ = void 0),
                  (this.timeoutId_ = void 0);
              }),
              (e.prototype.setMouseAnchor = function (t) {
                (this.useAnchor_ = t), t || (this.lastAnchor_ = null);
              }),
              e
            );
          })(Ll),
          gh = (function () {
            var t = function (e, n) {
              return (
                (t =
                  Object.setPrototypeOf ||
                  ({ __proto__: [] } instanceof Array &&
                    function (t, e) {
                      t.__proto__ = e;
                    }) ||
                  function (t, e) {
                    for (var n in e)
                      Object.prototype.hasOwnProperty.call(e, n) &&
                        (t[n] = e[n]);
                  }),
                t(e, n)
              );
            };
            return function (e, n) {
              if ("function" != typeof n && null !== n)
                throw new TypeError(
                  "Class extends value " +
                    String(n) +
                    " is not a constructor or null"
                );
              function i() {
                this.constructor = e;
              }
              t(e, n),
                (e.prototype =
                  null === n
                    ? Object.create(n)
                    : ((i.prototype = n.prototype), new i()));
            };
          })(),
          _h = (function (t) {
            function e(e) {
              var n = this,
                i = e || {},
                r = i;
              return (
                r.stopDown || (r.stopDown = c),
                ((n = t.call(this, r) || this).anchor_ = null),
                (n.lastAngle_ = void 0),
                (n.rotating_ = !1),
                (n.rotationDelta_ = 0),
                (n.threshold_ = void 0 !== i.threshold ? i.threshold : 0.3),
                (n.duration_ = void 0 !== i.duration ? i.duration : 250),
                n
              );
            }
            return (
              gh(e, t),
              (e.prototype.handleDragEvent = function (t) {
                var e = 0,
                  n = this.targetPointers[0],
                  i = this.targetPointers[1],
                  r = Math.atan2(i.clientY - n.clientY, i.clientX - n.clientX);
                if (void 0 !== this.lastAngle_) {
                  var o = r - this.lastAngle_;
                  (this.rotationDelta_ += o),
                    !this.rotating_ &&
                      Math.abs(this.rotationDelta_) > this.threshold_ &&
                      (this.rotating_ = !0),
                    (e = o);
                }
                this.lastAngle_ = r;
                var s = t.map,
                  a = s.getView();
                if (a.getConstraints().rotation !== gl) {
                  var l = s.getViewport().getBoundingClientRect(),
                    h = jl(this.targetPointers);
                  (h[0] -= l.left),
                    (h[1] -= l.top),
                    (this.anchor_ = s.getCoordinateFromPixelInternal(h)),
                    this.rotating_ &&
                      (s.render(), a.adjustRotationInternal(e, this.anchor_));
                }
              }),
              (e.prototype.handleUpEvent = function (t) {
                return !(
                  this.targetPointers.length < 2 &&
                  (t.map.getView().endInteraction(this.duration_), 1)
                );
              }),
              (e.prototype.handleDownEvent = function (t) {
                if (this.targetPointers.length >= 2) {
                  var e = t.map;
                  return (
                    (this.anchor_ = null),
                    (this.lastAngle_ = void 0),
                    (this.rotating_ = !1),
                    (this.rotationDelta_ = 0),
                    this.handlingDownUpSequence ||
                      e.getView().beginInteraction(),
                    !0
                  );
                }
                return !1;
              }),
              e
            );
          })(Gl),
          yh = (function () {
            var t = function (e, n) {
              return (
                (t =
                  Object.setPrototypeOf ||
                  ({ __proto__: [] } instanceof Array &&
                    function (t, e) {
                      t.__proto__ = e;
                    }) ||
                  function (t, e) {
                    for (var n in e)
                      Object.prototype.hasOwnProperty.call(e, n) &&
                        (t[n] = e[n]);
                  }),
                t(e, n)
              );
            };
            return function (e, n) {
              if ("function" != typeof n && null !== n)
                throw new TypeError(
                  "Class extends value " +
                    String(n) +
                    " is not a constructor or null"
                );
              function i() {
                this.constructor = e;
              }
              t(e, n),
                (e.prototype =
                  null === n
                    ? Object.create(n)
                    : ((i.prototype = n.prototype), new i()));
            };
          })(),
          vh = (function (t) {
            function e(e) {
              var n = this,
                i = e || {},
                r = i;
              return (
                r.stopDown || (r.stopDown = c),
                ((n = t.call(this, r) || this).anchor_ = null),
                (n.duration_ = void 0 !== i.duration ? i.duration : 400),
                (n.lastDistance_ = void 0),
                (n.lastScaleDelta_ = 1),
                n
              );
            }
            return (
              yh(e, t),
              (e.prototype.handleDragEvent = function (t) {
                var e = 1,
                  n = this.targetPointers[0],
                  i = this.targetPointers[1],
                  r = n.clientX - i.clientX,
                  o = n.clientY - i.clientY,
                  s = Math.sqrt(r * r + o * o);
                void 0 !== this.lastDistance_ && (e = this.lastDistance_ / s),
                  (this.lastDistance_ = s);
                var a = t.map,
                  l = a.getView();
                1 != e && (this.lastScaleDelta_ = e);
                var h = a.getViewport().getBoundingClientRect(),
                  u = jl(this.targetPointers);
                (u[0] -= h.left),
                  (u[1] -= h.top),
                  (this.anchor_ = a.getCoordinateFromPixelInternal(u)),
                  a.render(),
                  l.adjustResolutionInternal(e, this.anchor_);
              }),
              (e.prototype.handleUpEvent = function (t) {
                if (this.targetPointers.length < 2) {
                  var e = t.map.getView(),
                    n = this.lastScaleDelta_ > 1 ? 1 : -1;
                  return e.endInteraction(this.duration_, n), !1;
                }
                return !0;
              }),
              (e.prototype.handleDownEvent = function (t) {
                if (this.targetPointers.length >= 2) {
                  var e = t.map;
                  return (
                    (this.anchor_ = null),
                    (this.lastDistance_ = void 0),
                    (this.lastScaleDelta_ = 1),
                    this.handlingDownUpSequence ||
                      e.getView().beginInteraction(),
                    !0
                  );
                }
                return !1;
              }),
              e
            );
          })(Gl),
          mh = (function () {
            var t = function (e, n) {
              return (
                (t =
                  Object.setPrototypeOf ||
                  ({ __proto__: [] } instanceof Array &&
                    function (t, e) {
                      t.__proto__ = e;
                    }) ||
                  function (t, e) {
                    for (var n in e)
                      Object.prototype.hasOwnProperty.call(e, n) &&
                        (t[n] = e[n]);
                  }),
                t(e, n)
              );
            };
            return function (e, n) {
              if ("function" != typeof n && null !== n)
                throw new TypeError(
                  "Class extends value " +
                    String(n) +
                    " is not a constructor or null"
                );
              function i() {
                this.constructor = e;
              }
              t(e, n),
                (e.prototype =
                  null === n
                    ? Object.create(n)
                    : ((i.prototype = n.prototype), new i()));
            };
          })(),
          xh = (function (t) {
            function e(e) {
              return (
                (e = f({}, e)).controls ||
                  (e.controls = (function (t) {
                    var e = {},
                      n = new Ea();
                    return (
                      (void 0 === e.zoom || e.zoom) &&
                        n.push(new Tn(e.zoomOptions)),
                      (void 0 === e.rotate || e.rotate) &&
                        n.push(new Il(e.rotateOptions)),
                      (void 0 === e.attribution || e.attribution) &&
                        n.push(new Wt(e.attributionOptions)),
                      n
                    );
                  })()),
                e.interactions ||
                  (e.interactions = (function (t) {
                    var e = { onFocusOnly: !0 } || {},
                      n = new Ea(),
                      i = new ch(-0.005, 0.05, 100);
                    return (
                      (void 0 === e.altShiftDragRotate ||
                        e.altShiftDragRotate) &&
                        n.push(new Ql()),
                      (void 0 === e.doubleClickZoom || e.doubleClickZoom) &&
                        n.push(
                          new Dl({
                            delta: e.zoomDelta,
                            duration: e.zoomDuration,
                          })
                        ),
                      (void 0 === e.dragPan || e.dragPan) &&
                        n.push(
                          new ql({ onFocusOnly: e.onFocusOnly, kinetic: i })
                        ),
                      (void 0 === e.pinchRotate || e.pinchRotate) &&
                        n.push(new _h()),
                      (void 0 === e.pinchZoom || e.pinchZoom) &&
                        n.push(new vh({ duration: e.zoomDuration })),
                      (void 0 === e.keyboard || e.keyboard) &&
                        (n.push(new lh()),
                        n.push(
                          new uh({
                            delta: e.zoomDelta,
                            duration: e.zoomDuration,
                          })
                        )),
                      (void 0 === e.mouseWheelZoom || e.mouseWheelZoom) &&
                        n.push(
                          new dh({
                            onFocusOnly: e.onFocusOnly,
                            duration: e.zoomDuration,
                          })
                        ),
                      (void 0 === e.shiftDragZoom || e.shiftDragZoom) &&
                        n.push(new sh({ duration: e.zoomDuration })),
                      n
                    );
                  })()),
                t.call(this, e) || this
              );
            }
            return (
              mh(e, t),
              (e.prototype.createRenderer = function () {
                return new Ya(this);
              }),
              e
            );
          })(bl),
          Ch = {
            control: { Attribution: Wt, MousePosition: vn, Zoom: Tn },
            coordinate: {
              createStringXY: function (t) {
                return function (e) {
                  return (function (t, e) {
                    return (function (t, e, n) {
                      return t
                        ? "{x}, {y}"
                            .replace("{x}", t[0].toFixed(n))
                            .replace("{y}", t[1].toFixed(n))
                        : "";
                    })(t, 0, e);
                  })(e, t);
                };
              },
            },
            extent: { boundingExtent: ue },
            geom: {
              LineString: wi,
              LinearRing: Oi,
              MultiLineString: Pi,
              MultiPoint: ki,
              MultiPolygon: Hi,
              Point: Li,
              Polygon: Ki,
            },
            layer: { Tile: Xr, Vector: Ns },
            proj: {
              fromLonLat: function (t, e) {
                return Be(), en(t, "EPSG:4326", void 0 !== e ? e : "EPSG:3857");
              },
              get: Ue,
              transformExtent: nn,
            },
            source: { OSM: va, Vector: La },
            style: { Circle: fo, Fill: go, Stroke: _o, Style: Co, Text: Aa },
            Feature: ja,
            Map: xh,
            View: wl,
          };
      })(),
      i.default
    );
  })();
});
//# sourceMappingURL=OpenLayers.js.map
